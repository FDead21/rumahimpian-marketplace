<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourMedia extends Model
{
    protected $fillable = [
        'tour_id',
        'file_path',
        'sort_order',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}