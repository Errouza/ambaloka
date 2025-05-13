<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookable_type',
        'bookable_id',
        'status',
        'total_price',
        'booking_date',
        'payment_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookable()
    {
        return $this->morphTo();
    }
}
