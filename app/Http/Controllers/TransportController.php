<?php

namespace App\Http\Controllers;

use App\Services\TransportService;
use Illuminate\Http\Request;
use Exception;

class TransportController extends Controller
{
    protected $transportService;

    public function __construct(TransportService $transportService)
    {
        $this->transportService = $transportService;
    }

    /**
     * Menampilkan daftar transportasi
     */
    public function index()
    {
        try {
            $response = $this->transportService->getTransports();
            
            if ($response['success']) {
                $transports = $response['data'];
                return view('transports.index', compact('transports'));
            }

            return view('transports.index', ['transports' => [], 'error' => 'Tidak dapat mengambil data transportasi']);
        } catch (Exception $e) {
            return view('transports.index', ['transports' => [], 'error' => 'Layanan sedang tidak tersedia']);
        }
    }

    /**
     * Mencari transportasi berdasarkan kriteria
     */
    public function search(Request $request)
    {
        try {
            // Ambil daftar transportasi yang tersedia
            $transportResponse = $this->transportService->getTransports();
            $transports = [];
            
            if ($transportResponse['success']) {
                $transports = $transportResponse['data'];
            }

            // Cari transportasi sesuai kriteria
            $searchParams = [
                'departure_city' => $request->from,
                'arrival_city' => $request->to,
                'departure_date' => $request->date,
            ];

            $response = $this->transportService->searchTransports($searchParams);

            if ($response['success']) {
                $results = $response['data'];
                return view('transports.index', compact('results', 'transports'));
            }

            return view('transports.index', [
                'results' => [], 
                'transports' => $transports,
                'error' => 'Tidak ditemukan transportasi yang sesuai'
            ]);
        } catch (Exception $e) {
            return view('transports.index', [
                'results' => [], 
                'transports' => [],
                'error' => 'Layanan sedang tidak tersedia'
            ]);
        }
    }

    /**
     * Menampilkan detail transportasi
     */
    public function show($id)
    {
        try {
            $response = $this->transportService->getTransport($id);

            if ($response['success']) {
                $transport = $response['data'];
                
                // Check if we have updated seat count in session
                $sessionKey = "transport_{$id}_seat";
                if (session()->has($sessionKey)) {
                    // Override the seat count with the one from session
                    $transport['seat'] = session()->get($sessionKey);
                }
                
                return view('transports.show', compact('transport'));
            }

            return redirect()->route('transports.index')
                ->with('error', 'Transportasi tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->route('transports.index')
                ->with('error', 'Layanan sedang tidak tersedia');
        }
    }

    /**
     * Proses pemesanan transportasi
     */
    public function book($id, Request $request)
    {
        $validated = $request->validate([
            'seat' => 'required|integer|min:1',
        ]);

        try {
            // Cek ketersediaan kursi
            $transportResponse = $this->transportService->getTransport($id);
            
            if ($transportResponse['success']) {
                $transport = $transportResponse['data'];
                if ($transport['seat'] < $validated['seat']) {
                    return back()->with('error', 'Jumlah kursi tidak mencukupi');
                }
            } else {
                return back()->with('error', 'Transportasi tidak ditemukan');
            }

            // Create booking data with seat information
            $bookingData = [
                'id_transport' => $id,
                'seat' => $validated['seat'], // Using seat instead of passenger_count
                'user_id' => auth()->id(),
            ];
            
            // Update the transport seat count in Travelasing first
            $updateSeatsResponse = $this->transportService->updateTransportSeats($id, $validated['seat']);
            
            if (!$updateSeatsResponse['success']) {
                logger()->error('Gagal update kursi: ' . json_encode($updateSeatsResponse));
                return back()->with('error', 'Gagal memperbarui ketersediaan kursi');
            }
            
            // Now create the booking in the API
            $response = $this->transportService->createBooking($bookingData);

            if ($response['success']) {
                $apiBooking = $response['data'];
                
                // Also store the booking in the local database with all transport details
                $transport = $transportResponse['data'];
                
                // Calculate price based on a default value or use actual price if available
                $price = isset($transport['price']) ? $transport['price'] : 1000000; // Default price if not available
                $totalPrice = $price * $validated['seat'];
                
                // Get transport details for the booking record
                $transportCode = $transport['code'] ?? ''; // Airline code (e.g., GA)
                $transportDescription = $transport['description'] ?? ''; // Airline name (e.g., Garuda Indonesia)
                $transportType = isset($transport['transport_type']) ? $transport['transport_type']['description'] ?? 'Pesawat' : 'Pesawat';
                
                // Create a more detailed booking record
                $bookingDetails = [
                    'transport_code' => $transportCode,
                    'transport_name' => $transportDescription,
                    'transport_type' => $transportType,
                    'seat_count' => $validated['seat'],
                    'original_seat_available' => $transport['seat'],
                    'booking_time' => now()->toDateTimeString(),
                ];
                
                // Create local booking record
                try {
                    // Verificar que el usuario esté autenticado
                    if (!auth()->check()) {
                        logger()->error('Error booking: Usuario no autenticado');
                        return back()->with('error', 'Debe iniciar sesión para realizar una reserva');
                    }
                    
                    // Preparar datos de la reserva
                    $bookingData = [
                        'bookable_type' => 'Flight',
                        'bookable_id' => $id,
                        'booking_date' => now()->format('Y-m-d'),
                        'total_price' => $totalPrice,
                        'status' => 'confirmed',
                        'payment_status' => 'paid',
                        'passenger_count' => $validated['seat'],
                        'api_booking_id' => $apiBooking['id'] ?? null,
                        'booking_details' => $bookingDetails // No usar json_encode aquí, el modelo lo hará automáticamente
                    ];
                    
                    logger()->info('Intentando crear reserva local con datos: ' . json_encode($bookingData));
                    
                    $localBooking = auth()->user()->bookings()->create($bookingData);
                    
                    logger()->info('Reserva local creada con ID: ' . $localBooking->id);
                } catch (\Exception $e) {
                    logger()->error('Error al crear reserva local: ' . $e->getMessage());
                    logger()->error('Trace: ' . $e->getTraceAsString());
                    return back()->with('error', 'Error al guardar la reserva: ' . $e->getMessage());
                }
                
                // Redirect to the booking details page
                return redirect()->route('bookings.show', $localBooking)
                    ->with('success', 'Pemesanan berhasil dilakukan. Kursi tersedia telah diperbarui.');
            }

            return back()->with('error', 'Gagal melakukan pemesanan');
        } catch (Exception $e) {
            logger()->error('Error booking: ' . $e->getMessage());
            return back()->with('error', 'Layanan sedang tidak tersedia');
        }
    }
}
