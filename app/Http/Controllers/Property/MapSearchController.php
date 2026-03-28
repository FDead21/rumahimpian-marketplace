<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class MapSearchController extends Controller
{
    public function index()
    {
        $properties = \App\Models\Property::where('status', 'PUBLISHED')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get();

        return view('property.map-search', compact('properties'));
    }
}
