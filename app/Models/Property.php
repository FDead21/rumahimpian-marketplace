<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'views',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2',
        'views' => 'integer',
        'price' => 'decimal:2',
        'specifications' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($property) {
            // Auto-assign agent if logged in
            if (empty($property->user_id) && auth()->check()) {
                $property->user_id = auth()->id();
            }
    
            // Auto-generate slug from title if not set
            if (empty($property->slug)) {
                $property->slug = static::generateUniqueSlug($property->title);
            }
        });
    
        static::updating(function ($property) {
            // Regenerate slug if title changed and slug wasn't manually overridden
            if ($property->isDirty('title') && !$property->isDirty('slug')) {
                $property->slug = static::generateUniqueSlug($property->title, $property->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
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

    public function agency()
    {
        return $this->hasOneThrough(
            Agency::class,
            User::class,
            'id',      
            'id',       
            'user_id',  
            'agency_id' 
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PropertyMedia::class)->orderBy('sort_order');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }
    
    public function tourMedia()
    {
        return $this->hasMany(PropertyMedia::class)
            ->where('file_type', 'VIRTUAL_TOUR_360')
            ->orderBy('sort_order');
    }
    
    public function coverImage(): ?string
    {
        return $this->media->first()?->file_path;
    }
    
    
}
