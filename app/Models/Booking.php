<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'package_id',
        'property_id',
        'assigned_to',
        'client_name',
        'client_phone',
        'client_email',
        'event_date',
        'guest_count',
        'event_type',
        'notes',
        'total_price',
        'deposit_amount',
        'payment_status',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'event_date'     => 'date',
        'total_price'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $year  = now()->format('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $booking->booking_code = 'WO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'booking_vendors')
                    ->withPivot('notes')
                    ->withTimestamps();
    }

    // Helpers
    public function getWhatsappUrlAttribute(): string
    {
        $phone   = preg_replace('/[^0-9]/', '', $this->client_phone);
        $message = urlencode("Hello {$this->client_name}, this is regarding your booking {$this->booking_code} for {$this->event_date->format('d M Y')}.");
        return "https://wa.me/{$phone}?text={$message}";
    }

    public function getIsConfirmedAttribute(): bool
    {
        return in_array($this->status, ['CONFIRMED', 'DEPOSIT_PAID', 'IN_PROGRESS', 'COMPLETED']);
    }
}
