@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>                    <h1 class="text-3xl font-bold mb-2">{{ $flight['airline'] }}</h1>
                    <p class="text-gray-600">Flight {{ $flight['flight_number'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($flight['price'], 0, ',', '.') }}</p>
                    <p class="text-gray-500">per person</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="border rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Departure</h2>
                    <p class="text-2xl mb-1">{{ $flight['departure_city'] }}</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($flight['departure_time'])->format('l, d M Y') }}</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($flight['departure_time'])->format('H:i') }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Arrival</h2>
                    <p class="text-2xl mb-1">{{ $flight['arrival_city'] }}</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($flight['arrival_time'])->format('l, d M Y') }}</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($flight['arrival_time'])->format('H:i') }}</p>
                </div>
            </div>

            @auth            <form action="{{ route('flights.book', $flight['id']) }}" method="POST" class="border-t pt-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Number of Passengers</label>
                    <select name="passenger_count" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ Str::plural('passenger', $i) }}</option>
                        @endfor
                    </select>
                </div>

                @if($errors->any())
                    <div class="mb-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700">
                    Book Now
                </button>
            </form>
            @else
            <div class="border-t pt-6">
                <p class="text-center">
                    Please <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">login</a> 
                    or <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">register</a> 
                    to book this flight.
                </p>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection
