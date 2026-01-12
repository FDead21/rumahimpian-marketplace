<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Inquiry;

class Dashboard extends Page
{
    protected static ?string $title = 'Agent Dashboard';

    protected static string $view = 'filament.pages.dashboard';

    // Variables to pass to the view
    public $totalProperties;
    public $totalViews;
    public $recentLeads;
    public $properties;

    public function mount()
    {
        $user = Auth::user();

        // 1. Calculate Stats (Same logic as before)
        $this->totalProperties = Property::where('user_id', $user->id)->count();
        $this->totalViews = Property::where('user_id', $user->id)->sum('views');
        
        // 2. Get Recent Leads
        $this->recentLeads = Inquiry::whereHas('property', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->latest()->take(5)->get();

        // 3. Get Recent Properties
        $this->properties = Property::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
    }
}