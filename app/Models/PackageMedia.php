<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageMedia extends Model
{
    protected $fillable = ['package_id', 'file_path', 'sort_order'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}