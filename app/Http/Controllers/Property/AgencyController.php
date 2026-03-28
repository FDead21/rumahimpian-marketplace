<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;

class AgencyController extends Controller
{
    

    public function show($slug)
    {
        $agency = Agency::where('slug', $slug)->firstOrFail();
        
        $properties = \App\Models\Property::whereHas('user', function($q) use ($agency) {
            $q->where('agency_id', $agency->id);
        })->where('status', 'PUBLISHED')->latest()->paginate(9);

        return view('property.agency.show', compact('agency', 'properties'));
    }



}
