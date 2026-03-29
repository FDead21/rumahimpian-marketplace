<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        // Fetch data for the ribbons
        $featuredProperties = \App\Models\Property::where('status', 'PUBLISHED')->where('listing_type', 'RENT')->latest()->with('media')->take(4)->get();
        $featuredPackages = \App\Models\Package::where('is_active', true)->where('is_featured', true)->take(4)->get();
        $articles = \App\Models\Article::latest('published_at')->take(3)->get();

        return view('home-page', compact('settings', 'featuredProperties', 'featuredPackages', 'articles'));
    }
}