<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TourBooking extends Model
{
    protected $fillable = [
        'booking_code',
        'tour_id',
        'client_name',
        'client_phone',
        'client_email',
        'tour_date',
        'participants',
        'total_price',
        'deposit_amount',
        'payment_status',
        'status',
        'notes',
        'admin_notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'tour_date'      => 'date',
        'total_price'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'TOUR-' . strtoupper(Str::random(8));
            }
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'INQUIRY'     => 'warning',
            'CONFIRMED'   => 'success',
            'IN_PROGRESS' => 'info',
            'COMPLETED'   => 'success',
            'CANCELLED'   => 'danger',
            default       => 'secondary',
        };
    }
}