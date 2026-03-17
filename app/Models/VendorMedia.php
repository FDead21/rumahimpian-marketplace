<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'file_path',
        'sort_order',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
