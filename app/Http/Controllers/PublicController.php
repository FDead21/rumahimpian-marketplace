<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        // Start with Published properties only
        $query = Property::where('status', 'PUBLISHED');

        // 1. Search by Keyword (Title or City or District)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('district', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Property Type (e.g., 'Villa', 'House')
        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        // 3. Filter by Listing Type (Sale vs Rent)
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // 4. Min Price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // 5. Max Price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 6. Sort by Newest
        $properties = $query->latest()->paginate(9)->withQueryString(); // Keep filters in URL when clicking page 2

        return view('home', compact('properties'));
    }

    public function show($id, $slug)
    {
        // 1. Find by ID first (Faster & Unique)
        // We also make sure it is PUBLISHED so agents can't view hidden drafts by guessing ID
        $property = \App\Models\Property::where('status', 'PUBLISHED')->findOrFail($id);

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
            ->take(3)
            ->get();

        // Fallback if no match in city
        if ($relatedProperties->isEmpty()) {
            $relatedProperties = \App\Models\Property::where('category', $property->category)
                ->where('id', '!=', $property->id)
                ->where('status', 'PUBLISHED')
                ->take(3)
                ->get();
        }

        return view('property.show', compact('property', 'relatedProperties'));
    }

    public function tour($id, $slug)
    {
        $property = \App\Models\Property::where('status', 'PUBLISHED')->findOrFail($id);
        
        // Attempt to find a specific 360 image
        // If you don't have a 'file_type' column yet, we will just grab the LAST image as a demo
        $tourImage = $property->media->where('file_type', 'VIRTUAL_TOUR_360')->first();
        
        // FALLBACK: If no specific 360 image exists, use the main image 
        // (It will look weird/warped, but it proves the code works)
        if (!$tourImage) {
            $tourImage = $property->media->first();
        }

        return view('property.tour', compact('property', 'tourImage'));
    }

    // public function tour($slug)
    // {
    //     $property = Property::where('slug', $slug)->firstOrFail();
        
    //     // Fetch ALL images tagged as 360, sorted by order
    //     $scenes = $property->media()
    //         ->where('file_type', 'VIRTUAL_TOUR_360')
    //         ->orderBy('sort_order')
    //         ->get();

    //     if ($scenes->isEmpty()) {
    //         return redirect()->route('property.show', $slug)->with('error', 'No Virtual Tour available.');
    //     }

    //     return view('property.tour', compact('property', 'scenes'));
    // }

    public function agentProfile($id)
    {
        // Find agent or fail
        $agent = User::where('id', $id)->where('role', 'AGENT')->firstOrFail();

        // Get their properties (Paginated)
        $properties = Property::where('user_id', $agent->id)
            ->where('status', 'PUBLISHED')
            ->latest()
            ->paginate(9);

        return view('agent.show', compact('agent', 'properties'));
    }

    public function downloadPdf($id, $slug)
    {
        $property = \App\Models\Property::where('status', 'PUBLISHED')->findOrFail($id);

        $pdf = Pdf::loadView('property.pdf', compact('property'))
                ->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]); 

        return $pdf->stream('brochure-' . $property->slug . '.pdf');
    }

    public function agency($slug)
    {
        $agency = Agency::where('slug', $slug)->firstOrFail();
        
        $properties = \App\Models\Property::whereHas('user', function($q) use ($agency) {
            $q->where('agency_id', $agency->id);
        })->where('status', 'PUBLISHED')->latest()->paginate(9);

        return view('agency.show', compact('agency', 'properties'));
    }

    public function agent($id)
    {
        $agent = User::where('id', $id)->where('role', 'AGENT')->firstOrFail();
        
        $properties = \App\Models\Property::where('user_id', $agent->id)
                        ->where('status', 'PUBLISHED')
                        ->latest()
                        ->paginate(9);

        return view('agent.show', compact('agent', 'properties'));
    }

    public function wishlistParams(Request $request)
    {
        // 1. Get IDs from URL (?ids=1,5,8)
        $ids = explode(',', $request->ids);

        // 2. Fetch Properties
        $properties = \App\Models\Property::whereIn('id', $ids)
                        ->where('status', 'PUBLISHED')
                        ->get();

        // 3. Return only the HTML (Not a full page layout)
        $html = '';
        foreach ($properties as $property) {
            // Render the existing card component manually
            $html .= view('components.property-card', ['property' => $property])->render();
        }

        return response()->json(['html' => $html]);
    }

    public function mapSearch()
    {
        $properties = \App\Models\Property::where('status', 'PUBLISHED')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get();

        return view('map-search', compact('properties'));
    }

    public function compare(Request $request)
    {
        // Get IDs from URL (e.g., ?ids=1,2,3)
        $ids = explode(',', $request->query('ids', ''));
        
        // Fetch properties
        $properties = \App\Models\Property::whereIn('id', $ids)
            ->where('status', 'PUBLISHED')
            ->limit(3) // Limit to 3 for mobile safety
            ->get();

        return view('compare', compact('properties'));
    }
    
}