<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Get My Properties
        $properties = \App\Models\Property::where('user_id', $user->id)
            ->latest()
            ->paginate(5);

        // 2. Calculate Stats
        $totalProperties = \App\Models\Property::where('user_id', $user->id)->count();
        $totalViews = \App\Models\Property::where('user_id', $user->id)->sum('views');
        
        // 3. Get Recent Inquiries (Leads) linked to my properties
        // We look for inquiries where the property belongs to this user
        $recentLeads = \App\Models\Inquiry::whereHas('property', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->latest()->take(5)->get();

        return view('agent.dashboard', compact('properties', 'totalProperties', 'totalViews', 'recentLeads'));
    }
}