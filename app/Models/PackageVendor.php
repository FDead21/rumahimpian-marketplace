<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageVendor extends Model
{
    protected $table = 'package_vendor';
    protected $fillable = ['package_id', 'vendor_id', 'is_mandatory'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}