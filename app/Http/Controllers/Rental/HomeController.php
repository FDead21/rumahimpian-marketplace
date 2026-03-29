<?php

namespace App\Http\Controllers\Rental;

use App\Models\RentalVehicle;
use App\Models\Setting;

class HomeController extends RentalBaseController
{
    public function index()
    {
        $featured = RentalVehicle::where('is_active', true)
            ->where('is_featured', true)
            ->with('media')
            ->take(6)
            ->get();

        return view('rental.home', [
            'featured'       => $featured,
            'rentalSettings' => Setting::forGroup('VEHICLE'),
        ]);
    }
}