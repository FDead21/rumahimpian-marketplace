<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'price',
        'listing_type',
        'category',
        'property_type',
        'city',
        'district',
        'address',
        'latitude',
        'longitude',
        'bedrooms',
        'bathrooms',
        'land_area',
        'building_area',
        'specifications',
        'status',
        'youtube_url',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($property) {
            // If user_id is not manually set, and a user is logged in, use their ID.
            if (empty($property->user_id) && auth()->check()) {
                $property->user_id = auth()->id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PropertyMedia::class)->orderBy('sort_order');
    }
}
