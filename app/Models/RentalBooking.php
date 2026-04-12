<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RentalBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'rental_vehicle_id',
        'client_name',
        'client_phone',
        'client_email',
        'start_date',
        'end_date',
        'total_price',
        'deposit_amount',
        'payment_status',
        'status',
        'notes',
        'admin_notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'total_price'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'RENT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function rentalVehicle()
    {
        return $this->belongsTo(RentalVehicle::class);
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