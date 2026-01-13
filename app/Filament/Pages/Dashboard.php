<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Agency;

class Dashboard extends Page
{
    protected static ?string $title = 'Dashboard';
    protected static string $view = 'filament.pages.dashboard';

    // Shared Vars
    public $user;
    public $role;

    // Agent Vars
    public $totalProperties;
    public $totalViews;
    public $recentLeads;
    public $properties;

    // Admin Vars
    public $totalUsers;
    public $totalAgencies;
    public $pendingProperties;

    public function mount()
    {
        $this->user = Auth::user();
        $this->role = $this->user->role;

        if ($this->role === 'ADMIN') {
            // --- ADMIN DATA ---
            $this->totalUsers = User::count();
            $this->totalProperties = Property::count(); // Global Count
            $this->totalAgencies = Agency::count();
            
            // Pending Listings needing approval?
            $this->pendingProperties = Property::where('status', 'DRAFT')->count(); 
            
            // Admins still might want to see recent properties added to the platform
            $this->properties = Property::latest()->take(5)->get();
        } else {
            // --- AGENT DATA ---
            $this->totalProperties = Property::where('user_id', $this->user->id)->count();
            $this->totalViews = Property::where('user_id', $this->user->id)->sum('views');
            
            $this->recentLeads = Inquiry::whereHas('property', function($q) {
                $q->where('user_id', $this->user->id);
            })->latest()->take(5)->get();

            $this->properties = Property::where('user_id', $this->user->id)
                ->latest()
                ->take(5)
                ->get();
        }
    }
}