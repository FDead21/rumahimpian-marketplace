<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'event_type',
        'event_date',
        'cover_photo',
        'description',
        'is_published',
    ];

    protected $casts = [
        'event_date'   => 'date',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    public function media()
    {
        return $this->hasMany(GalleryMedia::class, 'gallery_event_id')->orderBy('sort_order');
    }
}
