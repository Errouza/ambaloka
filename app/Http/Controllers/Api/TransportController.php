<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TransportService;
use Exception;

class TransportController extends Controller
{
    protected $transportService;

    public function __construct(TransportService $transportService)
    {
        $this->transportService = $transportService;
    }
    
    /**
     * Update transport data
     *
     * @param int $id Transport ID
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            $validated = $request->validate([
                'seat' => 'required|integer|min:0',
            ]);
            
            $updateData = [
                'id' => $id,
                'seat' => $validated['seat']
            ];
            
            // Get current transport data to make sure it exists
            $transportResponse = $this->transportService->getTransport($id);
            
            if (!$transportResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transportasi tidak ditemukan'
                ], 404);
            }
            
            // Make API call to update the transport
            $response = $this->transportService->updateTransport($updateData);
            
            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data transportasi berhasil diperbarui',
                    'data' => $response['data'] ?? []
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data transportasi',
                'error' => $response['message'] ?? 'Unknown error'
            ], 500);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data transportasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update available seats for a transport after booking
     *
     * @param int $id Transport ID
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSeats($id, Request $request)
    {
        try {
            $validated = $request->validate([
                'booked_seats' => 'required|integer|min:1',
            ]);

            // Get current transport data
            $transportResponse = $this->transportService->getTransport($id);
            
            if (!$transportResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transportasi tidak ditemukan'
                ], 404);
            }
            
            $transport = $transportResponse['data'];
            $currentSeats = $transport['seat'];
            $bookedSeats = $validated['booked_seats'];
            
            // Calculate new available seats
            $newAvailableSeats = max(0, $currentSeats - $bookedSeats);
            
            // Update transport data with new seat count
            $updateData = [
                'id' => $id,
                'seat' => $newAvailableSeats
            ];
            
            // Make API call to update the transport
            $response = $this->transportService->updateTransport($updateData);
            
            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jumlah kursi berhasil diperbarui',
                    'data' => [
                        'previous_seats' => $currentSeats,
                        'booked_seats' => $bookedSeats,
                        'available_seats' => $newAvailableSeats
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jumlah kursi',
                'error' => $response['message'] ?? 'Unknown error'
            ], 500);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui jumlah kursi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
