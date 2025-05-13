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
}
