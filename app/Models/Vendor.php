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
        'city',
        'address',
        'description',
        'detailed_description',
        'logo',
        'instagram_url',
        'website_url',
        'youtube_url',
        'features',     
        'service_menu', 
        'price_from',
        'price_to',
        'is_active',
    ];

    protected $casts = [
        'price_from' => 'decimal:2',
        'price_to'   => 'decimal:2',
        'is_active'  => 'boolean',
        'features'     => 'array',
        'service_menu' => 'array',
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
