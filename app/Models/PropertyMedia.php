<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMedia extends Model
{
    protected $fillable = ['property_id', 'file_path', 'file_type', 'sort_order', 'room_name'];

    public function hotspots()
    {
        return $this->hasMany(TourHotspot::class, 'from_media_id');
    }
}