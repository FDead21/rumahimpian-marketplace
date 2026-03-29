<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        // 1. Properties
        $featuredProperties = \App\Models\Property::where('status', 'PUBLISHED')
            ->where('listing_type', 'RENT')
            ->latest()
            ->with('media')
            ->take(4)
            ->get();

        // 2. Events
        $featuredPackages = \App\Models\Package::where('is_active', true)
            ->where('is_featured', true)
            ->take(4)
            ->get();

        // 3. Articles
        $articles = \App\Models\Article::latest('published_at')->take(3)->get();

        // 4. NEW: Featured Tours
        $featuredTours = \App\Models\Tour::where('is_active', true)
            ->where('is_featured', true)
            ->with('media')
            ->take(4)
            ->get();

        // 5. NEW: Rentals (Grouped by Type for 1 row each)
        $rentalCars = \App\Models\RentalVehicle::where('is_active', true)
            ->where('vehicle_type', 'CAR')
            ->with('media')->take(4)->get();
            
        $rentalBikes = \App\Models\RentalVehicle::where('is_active', true)
            ->where('vehicle_type', 'MOTORBIKE')
            ->with('media')->take(4)->get();
            
        $rentalBoats = \App\Models\RentalVehicle::where('is_active', true)
            ->where('vehicle_type', 'BOAT')
            ->with('media')->take(4)->get();

        return view('home-page', compact(
            'settings', 'featuredProperties', 'featuredPackages', 'articles', 
            'featuredTours', 'rentalCars', 'rentalBikes', 'rentalBoats'
        ));
    }
}