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
        'thumbnail',
        'inclusions',
        'max_pax',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'price'      => 'decimal:2',
        'is_active'  => 'boolean',
        'is_featured'=> 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
