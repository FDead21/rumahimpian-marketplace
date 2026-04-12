<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'thumbnail',
        'inclusions',
        'max_pax',
        'address',
        'latitude',
        'longitude',
        'is_active',
        'is_featured',
        'blocked_dates',
    ];

    protected $casts = [
        'inclusions'     => 'array',
        'price'          => 'decimal:2',
        'original_price' => 'decimal:2',
        'latitude'       => 'decimal:7',
        'longitude'      => 'decimal:7',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'blocked_dates' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'package_vendor')
                    ->withPivot('is_mandatory')
                    ->withTimestamps();
    }

    public function media()
    {
        return $this->hasMany(PackageMedia::class)->orderBy('sort_order');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return null;
    }

    public function getDiscountAmountAttribute(): ?float
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return $this->original_price - $this->price;
        }
        return null;
    }

    public function packageVendors()
    {
        return $this->hasMany(PackageVendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWhatsappUrlAttribute(): string
        {
            // Get the creator's phone number, fallback to a default just in case
            // Note: Change 'phone_number' if your DB column is just named 'phone'
            $rawPhone = $this->user ? $this->user->phone_number : '6281296760196'; 
            
            // Clean the string so it only contains numbers (removes +, -, spaces)
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            
            $message = urlencode("Halo, saya tertarik dengan paket {$this->name} (Rp " . number_format($this->price, 0, ',', '.') . "). Mohon info lebih lanjut!");
            
            return "https://wa.me/{$cleanPhone}?text={$message}";
        }
}