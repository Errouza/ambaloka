@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h1 class="text-3xl font-bold mb-4">Find Your Perfect Hotel</h1>
        <form action="{{ route('hotels.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Destination</label>
                <input type="text" name="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="City or Area">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                <input type="date" name="check_in" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">Search Hotels</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($hotels as $hotel)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img src="{{ $hotel['image_url'] ?? 'https://via.placeholder.com/300x200' }}" alt="{{ $hotel['name'] }}" class="w-full h-48 object-cover">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold">{{ $hotel['name'] }}</h2>
                        <p class="text-gray-600">{{ $hotel['location'] }}</p>
                    </div>
                    <div class="flex items-center">
                        <span class="text-yellow-400">★</span>
                        <span class="ml-1">{{ $hotel['rating'] }}</span>
                    </div>
                </div>
                <p class="text-gray-700 mb-4">{{ Str::limit($hotel['description'], 100) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-blue-600">${{ number_format($hotel['price'], 2) }}</span>
                    <a href="{{ route('hotels.show', $hotel['id']) }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">View Details</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3">
            <p class="text-center text-gray-500">No hotels found. Try different search criteria.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
