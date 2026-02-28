<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourHotspot extends Model
{
    protected $fillable = ['from_media_id', 'to_media_id', 'pitch', 'yaw', 'label'];

    public function fromMedia() { return $this->belongsTo(PropertyMedia::class, 'from_media_id'); }
    public function toMedia()   { return $this->belongsTo(PropertyMedia::class, 'to_media_id'); }
}