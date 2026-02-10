<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'property_id',
        'buyer_name',
        'buyer_phone',
        'message',
        'status',    
        'admin_notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}