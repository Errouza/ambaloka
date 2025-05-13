<?php

namespace App\Http\Controllers;

use App\Services\TransportService;
use Illuminate\Http\Request;
use Exception;

/**
 * Controller untuk menangani operasi terkait penerbangan dan transportasi
 */
class FlightController extends Controller
{
    protected $transportService;

    public function __construct(TransportService $transportService)
    {
        $this->transportService = $transportService;
    }

    /**
     * Menampilkan daftar transportasi yang tersedia
     */
    public function index()
    {
        try {
            $response = $this->transportService->getTransports();
            
            if ($response['success']) {
                $transports = $response['data'];
                return view('flights.index', compact('transports'));
            }

            return view('flights.index', ['transports' => [], 'error' => 'Tidak dapat mengambil data transportasi']);
        } catch (Exception $e) {
            return view('flights.index', ['transports' => [], 'error' => 'Layanan sedang tidak tersedia']);
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
                $flights = $response['data'];
                return view('flights.index', compact('flights', 'transports'));
            }

            return view('flights.index', [
                'flights' => [], 
                'transports' => $transports,
                'error' => 'Tidak ditemukan transportasi yang sesuai'
            ]);
        } catch (Exception $e) {
            return view('flights.index', [
                'flights' => [], 
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
                return view('flights.show', compact('transport'));
            }

            return redirect()->route('flights.index')
                ->with('error', 'Transportasi tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->route('flights.index')
                ->with('error', 'Layanan sedang tidak tersedia');
        }
    }

    /**
     * Proses pemesanan transportasi
     */
    public function book($id, Request $request)
    {
        $validated = $request->validate([
            'passenger_count' => 'required|integer|min:1',
        ]);

        try {
            // Cek ketersediaan kursi
            $transportResponse = $this->transportService->getTransport($id);
            
            if ($transportResponse['success']) {
                $transport = $transportResponse['data'];
                if ($transport['seat'] < $validated['passenger_count']) {
                    return back()->with('error', 'Jumlah kursi tidak mencukupi');
                }
            }

            $bookingData = [
                'id_transport' => $id,
                'passenger_count' => $validated['passenger_count'],
                'user_id' => auth()->id(),
            ];

            $response = $this->transportService->createBooking($bookingData);

            if ($response['success']) {
                $booking = $response['data'];
                return redirect()->route('bookings.show', $booking['id'])
                    ->with('success', 'Pemesanan berhasil dilakukan');
            }

            return back()->with('error', 'Gagal melakukan pemesanan');
        } catch (Exception $e) {
            return back()->with('error', 'Layanan sedang tidak tersedia');
        }
    }
}
