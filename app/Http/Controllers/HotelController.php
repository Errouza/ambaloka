<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HotelController extends Controller
{
    public function index()
    {
        // Using RapidAPI Hotels API as an example
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => env('RAPID_API_HOST'),
            'X-RapidAPI-Key' => env('RAPID_API_KEY'),
        ])->get('https://hotels4.p.rapidapi.com/locations/v3/search', [
            'query' => 'jakarta',
            'locale' => 'en_US'
        ]);

        return view('hotels.index', [
            'hotels' => $response->json()['data'] ?? []
        ]);
    }

    public function show($id)
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => env('RAPID_API_HOST'),
            'X-RapidAPI-Key' => env('RAPID_API_KEY'),
        ])->get("https://hotels4.p.rapidapi.com/properties/v2/detail", [
            'propertyId' => $id
        ]);

        return view('hotels.show', [
            'hotel' => $response->json()['data'] ?? null
        ]);
    }
}
