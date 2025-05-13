@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">My Bookings</h1>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            @forelse($bookings as $booking)
            <div class="border-b border-gray-200 py-4 {{ !$loop->first ? 'mt-4' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-semibold">
                            {{ $booking->bookable->name ?? ($booking->bookable_type . ' #' . $booking->bookable_id) }}
                        </h3>
                        <p class="text-gray-600">Booked on: {{ $booking->created_at->format('M d, Y') }}</p>
                        <p class="text-gray-600">For: {{ $booking->booking_date->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
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
                        <p class="mt-2 text-lg font-bold">${{ number_format($booking->total_price, 2) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        View Details →
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <p class="text-gray-500">You haven't made any bookings yet.</p>
                <div class="mt-4">
                    <a href="{{ route('flights.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">Browse Flights</a>
                    <a href="{{ route('hotels.index') }}" class="text-blue-600 hover:text-blue-800">Browse Hotels</a>
                </div>
            </div>
            @endforelse
        </div>
        @if($bookings->hasPages())
        <div class="px-6 py-4 bg-gray-50">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
