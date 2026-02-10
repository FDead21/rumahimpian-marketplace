<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bank extends Model
{
    protected $fillable = [
        'name', 'code', 'logo', 'is_active',
        'fixed_rate_1y', 'fixed_rate_3y', 'fixed_rate_5y', 
        'floating_rate', 'max_tenor', 'min_dp_percent'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fixed_rate_1y' => 'float',
        'fixed_rate_3y' => 'float',
        'fixed_rate_5y' => 'float',
        'floating_rate' => 'float',
        'min_dp_percent' => 'float',
    ];
    
    public function getLogoUrl()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }
}