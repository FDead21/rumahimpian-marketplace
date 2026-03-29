<?php

namespace App\Http\Controllers\Rental;

use App\Models\RentalVehicle;
use App\Models\Setting;
use Illuminate\Http\Request;

class VehicleController extends RentalBaseController
{
    public function index(Request $request)
    {
        $query = RentalVehicle::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('vehicle_type', strtoupper($request->type));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $vehicles = $query->orderByDesc('is_featured')
            ->orderBy('price_per_day')
            ->with('media')
            ->get();

        return view('rental.vehicle.index', [
            'vehicles'       => $vehicles,
            'rentalSettings' => Setting::forGroup('VEHICLE'),
        ]);
    }

    public function show(string $slug)
    {
        $vehicle = RentalVehicle::where('slug', $slug)
            ->where('is_active', true)
            ->with('media')
            ->firstOrFail();

        $related = RentalVehicle::where('is_active', true)
            ->where('id', '!=', $vehicle->id)
            ->where('vehicle_type', $vehicle->vehicle_type)
            ->orderByDesc('is_featured')
            ->take(3)
            ->with('media')
            ->get();

        return view('rental.vehicle.show', [
            'vehicle'        => $vehicle,
            'related'        => $related,
            'rentalSettings' => Setting::forGroup('VEHICLE'),
        ]);
    }
}