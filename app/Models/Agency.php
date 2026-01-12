<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $guarded = [];

    public function agents()
    {
        return $this->hasMany(User::class, 'agency_id');
    }
}