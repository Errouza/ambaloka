@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('bookings.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            &larr; Back to My Bookings
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold mb-6">Booking Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Booking Information</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Booking ID:</label>
                        <span class="text-lg">{{ $booking->id }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Booking Type:</label>
                        <span class="text-lg">{{ $booking->bookable_type }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Booking Date:</label>
                        <span class="text-lg">{{ $booking->booking_date->format('d F Y') }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Passenger Count:</label>
                        <span class="text-lg">{{ $booking->passenger_count ?? 'Not specified' }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Status:</label>
                        <span class="inline-block px-3 py-1 rounded-full text-sm
                            @if($booking->status === 'confirmed')
                                bg-green-100 text-green-800
                            @elseif($booking->status === 'pending')
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-red-100 text-red-800
                            @endif
                        ">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Total Price:</label>
                        <span class="text-lg font-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Payment Status:</label>
                        <span class="inline-block px-3 py-1 rounded-full text-sm
                            @if($booking->payment_status === 'paid')
                                bg-green-100 text-green-800
                            @elseif($booking->payment_status === 'pending')
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-red-100 text-red-800
                            @endif
                        ">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Booked On:</label>
                        <span class="text-lg">{{ $booking->created_at->format('d F Y H:i') }}</span>
                    </div>
                </div>

                @if($booking->payment_status !== 'paid')
                <div class="mt-6">
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                        Pay Now
                    </button>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Booking Details</h2>
            
            @if($booking->bookable_type === 'Flight')
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium mb-2">Transport Information</h3>
                @if($booking->booking_details)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600"><span class="font-medium">Transport Code:</span> {{ $booking->booking_details['transport_code'] ?? 'N/A' }}</p>
                        <p class="text-gray-600"><span class="font-medium">Transport Name:</span> {{ $booking->booking_details['transport_name'] ?? 'N/A' }}</p>
                        <p class="text-gray-600"><span class="font-medium">Transport Type:</span> {{ $booking->booking_details['transport_type'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600"><span class="font-medium">Seat Count:</span> {{ $booking->booking_details['seat_count'] ?? $booking->passenger_count }}</p>
                        <p class="text-gray-600"><span class="font-medium">Original Available Seats:</span> {{ $booking->booking_details['original_seat_available'] ?? 'N/A' }}</p>
                        <p class="text-gray-600"><span class="font-medium">Booking Time:</span> {{ $booking->booking_details['booking_time'] ?? $booking->created_at }}</p>
                    </div>
                </div>
                @else
                <p class="text-gray-600">Transport ID: {{ $booking->bookable_id }}</p>
                <p class="text-gray-600">Passenger Count: {{ $booking->passenger_count ?? 'Not specified' }}</p>
                <p class="text-gray-600">API Booking ID: {{ $booking->api_booking_id ?? 'Not available' }}</p>
                @endif
            </div>
            @elseif($booking->bookable_type === 'Hotel')
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium mb-2">Hotel Information</h3>
                <p class="text-gray-600">Hotel ID: {{ $booking->bookable_id }}</p>
                <p class="text-gray-600">API Booking ID: {{ $booking->api_booking_id ?? 'Not available' }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
