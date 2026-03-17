<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_event_id',
        'file_path',
        'caption',
        'sort_order',
    ];

    public function event()
    {
        return $this->belongsTo(GalleryEvent::class, 'gallery_event_id');
    }
}
