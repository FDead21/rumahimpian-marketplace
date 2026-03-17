<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'phone',
        'email',
        'description',
        'logo',
        'price_from',
        'price_to',
        'is_active',
    ];

    protected $casts = [
        'price_from' => 'decimal:2',
        'price_to'   => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function media()
    {
        return $this->hasMany(VendorMedia::class)->orderBy('sort_order');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_vendors')
                    ->withPivot('notes')
                    ->withTimestamps();
    }
}
