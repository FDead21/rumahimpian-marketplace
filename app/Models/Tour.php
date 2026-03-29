<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'category',
        'custom_category',
        'duration_days',
        'duration_label',
        'itinerary',
        'meeting_point',
        'meeting_point_lat',
        'meeting_point_lng',
        'min_participants',
        'max_participants',
        'price_per_person',
        'original_price',
        'thumbnail',
        'inclusions',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'itinerary'      => 'array',
        'inclusions'     => 'array',
        'price_per_person' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($tour) {
            if (empty($tour->user_id) && auth()->check()) {
                $tour->user_id = auth()->id();
            }
            if (empty($tour->slug)) {
                $tour->slug = static::generateUniqueSlug($tour->name);
            }
        });

        static::updating(function ($tour) {
            if ($tour->isDirty('name') && !$tour->isDirty('slug')) {
                $tour->slug = static::generateUniqueSlug($tour->name, $tour->id);
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
        return $this->hasMany(TourMedia::class)->orderBy('sort_order');
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class);
    }

    public function coverImage(): ?string
    {
        return $this->media->first()?->file_path;
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category === 'CUSTOM' && $this->custom_category) {
            return $this->custom_category;
        }

        return match($this->category) {
            'ADVENTURE'   => 'Adventure',
            'CULTURAL'    => 'Cultural',
            'NATURE'      => 'Nature',
            'WATER_SPORTS' => 'Water Sports',
            default       => $this->category,
        };
    }

    public function getDiscountAmountAttribute(): float
    {
        if ($this->original_price && $this->original_price > $this->price_per_person) {
            return $this->original_price - $this->price_per_person;
        }
        return 0;
    }
}