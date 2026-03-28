<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingVendor extends Model
{
    protected $table = 'booking_vendors';
    protected $fillable = ['booking_id', 'vendor_id', 'agreed_price', 'notes'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}