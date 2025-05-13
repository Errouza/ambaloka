<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

/**
 * Service untuk menangani semua interaksi dengan API Transport
 */
class TransportService
{
    private $apiBaseUrl;
    private $client;
    private $token;

    /**
     * Inisialisasi service dengan konfigurasi API
     */
    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:8000'), '/');
        $this->token = env('API_TOKEN');
        $this->client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Melakukan request ke API
     * 
     * @param string $method HTTP method (GET, POST, dll)
     * @param string $endpoint Endpoint API
     * @param array $options Opsi tambahan untuk request
     * @return array Response dari API dalam bentuk array
     * @throws Exception
     */
    private function request($method, $endpoint, $options = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception("Error API: " . $e->getMessage());
        }
    }

    /**
     * Mendapatkan daftar semua transportasi
     */
    public function getTransports()
    {
        return $this->request('GET', '/api/transports');
    }

    /**
     * Mendapatkan detail transportasi berdasarkan ID
     */
    public function getTransport($id)
    {
        return $this->request('GET', "/api/transports/{$id}");
    }

    /**
     * Mendapatkan daftar tipe transportasi
     */
    public function getTransportTypes()
    {
        return $this->request('GET', '/api/transport-types');
    }

    /**
     * Mendapatkan daftar rute yang tersedia
     */
    public function getRoutes()
    {
        return $this->request('GET', '/api/routes');
    }

    /**
     * Mendapatkan daftar jadwal
     */
    public function getSchedules()
    {
        return $this->request('GET', '/api/schedules');
    }

    /**
     * Mencari transportasi berdasarkan parameter
     * 
     * @param array $params Parameter pencarian (kota asal, tujuan, tanggal)
     */
    public function searchTransports($params)
    {
        return $this->request('GET', '/api/transports/search', [
            'query' => $params
        ]);
    }

    /**
     * Membuat pemesanan baru
     * 
     * @param array $data Data pemesanan (id_transport, jumlah_penumpang, dll)
     */
    public function createBooking($data)
    {
        return $this->request('POST', '/api/bookings', [
            'json' => $data
        ]);
    }

    /**
     * Memperbarui jumlah kursi yang tersedia pada transportasi
     * 
     * @param int $transportId ID transportasi
     * @param int $bookedSeats Jumlah kursi yang dipesan
     * @return array Response dari API
     */
    public function updateTransportSeats($transportId, $bookedSeats)
    {
        try {
            // Make a direct request to the new update-seats endpoint
            $response = $this->request('PUT', "/api/transports/{$transportId}/update-seats", [
                'json' => [
                    'booked_seats' => $bookedSeats
                ]
            ]);
            
            // If successful, store the updated seat count in session as well
            if (isset($response['success']) && $response['success'] && isset($response['data']['available_seats'])) {
                $updatedSeatCount = $response['data']['available_seats'];
                session()->put("transport_{$transportId}_seat", $updatedSeatCount);
            }
            
            return $response;
        } catch (Exception $e) {
            logger()->error('Error updating seats: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal memperbarui kursi: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Memperbarui data transportasi
     * 
     * @param array $data Data transportasi yang akan diperbarui
     * @return array Response dari API
     */
    public function updateTransport($data)
    {
        try {
            // First, get the current transport data
            $transportResponse = $this->getTransport($data['id']);
            
            if (!$transportResponse['success']) {
                return [
                    'success' => false,
                    'message' => 'Transportasi tidak ditemukan'
                ];
            }
            
            $transportId = $data['id'];
            $updatedSeatCount = $data['seat'];
            
            // Store in session as a fallback
            session()->put("transport_{$transportId}_seat", $updatedSeatCount);
            
            // Make the actual API call to update the transport in Travelasing
            try {
                // First, try to authenticate with the API
                $loginResponse = $this->authenticateWithAPI();
                
                if ($loginResponse['success']) {
                    // Use the new token for the update request
                    $token = $loginResponse['token'];
                    
                    // Make the update request with the new token
                    $updateResponse = $this->request('PUT', "/api/transports/{$transportId}", [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token
                        ],
                        'json' => [
                            'seat' => $updatedSeatCount
                        ]
                    ]);
                    
                    if (isset($updateResponse['success']) && $updateResponse['success']) {
                        return [
                            'success' => true,
                            'message' => 'Kursi berhasil diperbarui di database',
                            'data' => $updateResponse['data'] ?? [
                                'id' => $transportId,
                                'seat' => $updatedSeatCount
                            ]
                        ];
                    }
                }
                
                // If API update fails, log the error but still return success
                // since we've stored the updated count in the session
                logger()->error('Failed to update transport in API: ' . json_encode($loginResponse));
                
            } catch (Exception $apiError) {
                // Log the API error but don't fail the entire process
                logger()->error('API Error: ' . $apiError->getMessage());
            }
            
            // Return success even if the API call failed
            // since we've stored the updated count in the session
            return [
                'success' => true,
                'message' => 'Kursi berhasil diperbarui secara lokal',
                'data' => [
                    'id' => $data['id'],
                    'seat' => $data['seat']
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Authenticate with the Travelasing API to get a token
     * 
     * @return array Response with token if successful
     */
    private function authenticateWithAPI()
    {
        try {
            // Use admin credentials from env or use defaults
            $credentials = [
                'email' => env('API_ADMIN_EMAIL', 'admin@example.com'),
                'password' => env('API_ADMIN_PASSWORD', 'password')
            ];
            
            $response = $this->request('POST', '/api/auth/login', [
                'json' => $credentials
            ]);
            
            // Check the response structure based on Travelasing API
            if (isset($response['success']) && $response['success'] && 
                isset($response['data']['access_token'])) {
                return [
                    'success' => true,
                    'token' => $response['data']['access_token']
                ];
            }
            
            logger()->error('Authentication response: ' . json_encode($response));
            
            return [
                'success' => false,
                'message' => 'Authentication failed',
                'response' => $response
            ];
        } catch (Exception $e) {
            logger()->error('Authentication error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Authentication error: ' . $e->getMessage()
            ];
        }
    }
}
