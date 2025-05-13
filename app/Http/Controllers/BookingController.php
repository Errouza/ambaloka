<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings()
            ->with('bookable')
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        return view('bookings.show', compact('booking'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bookable_type' => 'required|in:flight,hotel',
            'bookable_id' => 'required|integer',
            'booking_date' => 'required|date',
            'total_price' => 'required|numeric'
        ]);

        $booking = auth()->user()->bookings()->create([
            'bookable_type' => ucfirst($validated['bookable_type']),
            'bookable_id' => $validated['bookable_id'],
            'booking_date' => $validated['booking_date'],
            'total_price' => $validated['total_price'],
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }
}
