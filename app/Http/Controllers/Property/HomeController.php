<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Start with Published properties only
        $query = Property::where('status', 'PUBLISHED')
                         ->where('listing_type', 'RENT');

        // 1. Search by Keyword (Title or City or District)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('district', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Property Type
        if ($request->filled('type') && $request->type !== 'ALL') {
            $query->where('property_type', $request->type);
        }

        // 3. Filter by Listing Type (Sale vs Rent)
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // 4. Min Price (Handle "dot" formatting if needed, e.g. 1.000.000)
        if ($request->filled('min_price')) {
            $price = str_replace('.', '', $request->min_price);
            $query->where('price', '>=', (int)$price);
        }

        // 5. Max Price
        if ($request->filled('max_price')) {
            $price = str_replace('.', '', $request->max_price);
            $query->where('price', '<=', (int)$price);
        }

        // 6. Bedrooms (NEW ADDITION)
        if ($request->filled('bedrooms')) {
            if ($request->bedrooms == '5+') {
                $query->where('bedrooms', '>=', 5);
            } else {
                $query->where('bedrooms', $request->bedrooms);
            }
        }

        // Sort by Newest
        $properties = $query->latest()->paginate(9)->withQueryString();

        $latestArticles = \App\Models\Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('property.home', compact('properties', 'latestArticles'));
    }
}
