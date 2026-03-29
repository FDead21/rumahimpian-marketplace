<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalVehicleMedia extends Model
{
    protected $fillable = [
        'rental_vehicle_id',
        'file_path',
        'sort_order',
    ];

    public function vehicle()
    {
        return $this->belongsTo(RentalVehicle::class, 'rental_vehicle_id');
    }
}