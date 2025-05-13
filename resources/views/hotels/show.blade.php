@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($hotel['images'] ?? false)
        <div class="relative h-96">
            <img src="{{ $hotel['images'][0] ?? 'https://via.placeholder.com/800x400' }}" 
                 alt="{{ $hotel['name'] }}" 
                 class="w-full h-full object-cover">
        </div>
        @endif

        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold">{{ $hotel['name'] }}</h1>
                    <p class="text-gray-600 mt-2">{{ $hotel['location'] }}</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center mb-2">
                        <span class="text-yellow-400 text-xl">★</span>
                        <span class="ml-1 text-xl">{{ $hotel['rating'] }}</span>
                    </div>
                    <p class="text-3xl font-bold text-blue-600">${{ number_format($hotel['price'], 2) }}</p>
                    <p class="text-gray-500">per night</p>
                </div>
            </div>

            <div class="border-t border-gray-200 py-6">
                <h2 class="text-xl font-semibold mb-4">About this hotel</h2>
                <p class="text-gray-700">{{ $hotel['description'] }}</p>
            </div>

            @if($hotel['amenities'] ?? false)
            <div class="border-t border-gray-200 py-6">
                <h2 class="text-xl font-semibold mb-4">Amenities</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($hotel['amenities'] as $amenity)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ml-2">{{ $amenity }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="border-t border-gray-200 py-6">
                <h2 class="text-xl font-semibold mb-4">Book this hotel</h2>
                <form action="{{ route('hotels.book', $hotel['id']) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                            <input type="date" name="check_in" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                            <input type="date" name="check_out" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Number of Guests</label>
                        <select name="guests" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ Str::plural('guest', $i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Book Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
