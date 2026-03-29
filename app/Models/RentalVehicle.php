<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RentalVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'vehicle_type',
        'brand',
        'year',
        'price_per_day',
        'thumbnail',
        'city',
        'address',
        'latitude',
        'longitude',
        'max_passengers',
        'specifications',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price_per_day'  => 'decimal:2',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($vehicle) {
            if (empty($vehicle->user_id) && auth()->check()) {
                $vehicle->user_id = auth()->id();
            }
            if (empty($vehicle->slug)) {
                $vehicle->slug = static::generateUniqueSlug($vehicle->name);
            }
        });

        static::updating(function ($vehicle) {
            if ($vehicle->isDirty('name') && !$vehicle->isDirty('slug')) {
                $vehicle->slug = static::generateUniqueSlug($vehicle->name, $vehicle->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(RentalVehicleMedia::class)->orderBy('sort_order');
    }

    public function coverImage(): ?string
    {
        return $this->media->first()?->file_path;
    }
}