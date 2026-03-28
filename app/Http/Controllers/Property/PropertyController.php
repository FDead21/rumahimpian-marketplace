<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyController extends Controller
{
     public function show($id, $slug)
    {
        // 1. Find by ID first (Faster & Unique)
        // We also make sure it is PUBLISHED so agents can't view hidden drafts by guessing ID
        $property = \App\Models\Property::where('status', 'PUBLISHED')
                                        ->where('listing_type', 'RENT')
                                        ->findOrFail($id);

        // 2. SEO Redirect Check
        // If the URL slug is wrong (e.g. old name), 301 Redirect to the correct one
        if ($slug !== $property->slug) {
            return redirect()->route('property.show', [
                'id' => $property->id, 
                'slug' => $property->slug
            ], 301);
        }

        // 3. Increment Views
        $property->increment('views');

        // 4. RECOMMENDATION ENGINE (Your existing logic)
        $relatedProperties = \App\Models\Property::where('city', $property->city)
            ->where('category', $property->category) // Ensure 'category' column exists, or use 'property_type'
            ->where('id', '!=', $property->id)
            ->where('status', 'PUBLISHED')
            ->where('listing_type', 'RENT')
            ->take(3)
            ->get();

        // Fallback if no match in city
        if ($relatedProperties->isEmpty()) {
            $relatedProperties = \App\Models\Property::where('category', $property->category)
                ->where('id', '!=', $property->id)
                ->where('status', 'PUBLISHED')
                ->where('listing_type', 'RENT')
                ->take(3)
                ->get();
        }

        $banks = \App\Models\Bank::where('is_active', true)->orderBy('name')->get();

        return view('property.property-detail.show', compact('property', 'relatedProperties', 'banks'));
    }

    public function tour($id, $slug)
    {
        $property = \App\Models\Property::where('status', 'PUBLISHED')->findOrFail($id);

        $scenes = $property->media()
            ->where('file_type', 'VIRTUAL_TOUR_360')
            ->orderBy('sort_order')
            ->with('hotspots.toMedia')
            ->get();

        if ($scenes->isEmpty()) {
            return redirect()->route('property.show', ['id' => $id, 'slug' => $slug])
                ->with('error', 'No Virtual Tour available.');
        }

        return view('property.property-detail.tour', compact('property', 'scenes'));
    }

    public function downloadPdf($id, $slug)
    {
        $property = \App\Models\Property::where('status', 'PUBLISHED')->findOrFail($id);

        $pdf = Pdf::loadView('property.property-detail.pdf', compact('property'))
                ->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]); 

        return $pdf->stream('brochure-' . $property->slug . '.pdf');
    }

    public function wishlistParams(Request $request)
    {
        // 1. Get IDs from URL (?ids=1,5,8)
        $ids = explode(',', $request->ids);

        // 2. Fetch Properties
        $properties = \App\Models\Property::whereIn('id', $ids)
                        ->where('status', 'PUBLISHED')
                        ->where('listing_type', 'RENT')
                        ->get();

        // 3. Return only the HTML (Not a full page layout)
        $html = '';
        foreach ($properties as $property) {
            // Render the existing card component manually
            $html .= view('property.components.property-card', ['property' => $property])->render();
        }

        return response()->json(['html' => $html]);
    }

    public function compare(Request $request)
    {
        // Get IDs from URL (e.g., ?ids=1,2,3)
        $ids = explode(',', $request->query('ids', ''));
        
        // Fetch properties
        $properties = \App\Models\Property::whereIn('id', $ids)
            ->where('status', 'PUBLISHED')
            ->where('listing_type', 'RENT')
            ->limit(3) 
            ->get();

        return view('property.compare', compact('properties'));
    }
}
