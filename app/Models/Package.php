<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
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
    ];

    protected $casts = [
        'inclusions'     => 'array',
        'price'          => 'decimal:2',
        'original_price' => 'decimal:2',
        'latitude'       => 'decimal:7',
        'longitude'      => 'decimal:7',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
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
}