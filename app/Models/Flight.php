<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;    protected $fillable = [
        'airline',
        'flight_number',
        'departure_city',
        'arrival_city',
        'departure_time',
        'arrival_time',
        'price'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'decimal:2'
    ];

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
}
