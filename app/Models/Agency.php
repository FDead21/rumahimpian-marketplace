<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agency extends Model
{
    use HasFactory; 

    protected $guarded = [];

    public function agents()
    {
        return $this->hasMany(User::class, 'agency_id');
    }
}