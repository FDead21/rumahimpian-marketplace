<?php

namespace App\Http\Controllers\Tour;

use App\Models\Tour;

class HomeController extends TourBaseController
{
    public function index()
    {
        $featured = Tour::where('is_active', true)
            ->where('is_featured', true)
            ->with('media')
            ->take(6)
            ->get();

        $categories = Tour::where('is_active', true)
            ->selectRaw('category, custom_category, COUNT(*) as count')
            ->groupBy('category', 'custom_category')
            ->get();

        return view('tour.home', compact('featured', 'categories'));
    }
}