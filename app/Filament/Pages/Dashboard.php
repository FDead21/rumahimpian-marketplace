<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Agency;
use App\Models\TourBooking;
use App\Models\Booking;
use App\Models\RentalBooking;
use App\Filament\Widgets\MasterCalendarWidget;

class Dashboard extends Page
{
    protected static ?string $title = 'Dashboard';
    protected static string $view = 'filament.pages.dashboard';

    public $user;
    public $role;
    public $upcomingBookings;
    public $todaySchedule;
    // Property vars
    public $totalProperties;
    public $totalViews;
    public $recentLeads;
    public $properties;

    // Admin vars
    public $totalUsers;
    public $totalAgencies;
    public $pendingProperties;

    // New booking vars
    public $totalRevenue;
    public $totalBookings;
    public $pendingBookings;

    public $tourPending;
    public $rentalPending;
    public $eventPending;

    public function mount()
    {
        $this->user = Auth::user();
        $this->role = $this->user->role;
        $this->tourPending   = TourBooking::where('status', 'INQUIRY')->count();
        $this->rentalPending = RentalBooking::where('status', 'INQUIRY')->count();
        $this->eventPending  = Booking::where('status', 'INQUIRY')->count();
        $this->pendingBookings = $this->tourPending + $this->rentalPending + $this->eventPending;

        $upcomingTours = TourBooking::with('tour')
            ->whereIn('status', ['CONFIRMED', 'INQUIRY'])
            ->where('tour_date', '>=', now())
            ->orderBy('tour_date')
            ->take(10)
            ->get()
            ->map(fn($b) => [
                'type'   => 'tour',
                'icon'   => '🗺️',
                'color'  => '#0f766e',
                'name'   => $b->tour->name ?? 'Tour',
                'client' => $b->client_name,
                'date'   => $b->tour_date->format('d M Y'),
                'status' => $b->status,
                'price'  => $b->total_price,
                'url'    => \App\Filament\Resources\TourBookingResource::getUrl('edit', ['record' => $b->id]),
            ]);

        $upcomingRentals = RentalBooking::with('rentalVehicle')
            ->whereIn('status', ['CONFIRMED', 'INQUIRY'])
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(10)
            ->get()
            ->map(fn($b) => [
                'type'   => 'rental',
                'icon'   => '🚗',
                'color'  => '#1d4ed8',
                'name'   => $b->rentalVehicle->name ?? 'Vehicle',
                'client' => $b->client_name,
                'date'   => $b->start_date->format('d M Y'),
                'status' => $b->status,
                'price'  => $b->total_price,
                'url'    => \App\Filament\Resources\RentalBookingResource::getUrl('edit', ['record' => $b->id]),
            ]);

        $upcomingEvents = Booking::with('package')
            ->whereIn('status', ['CONFIRMED', 'INQUIRY'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(10)
            ->get()
            ->map(fn($b) => [
                'type'   => 'event',
                'icon'   => '🎉',
                'color'  => '#b45309',
                'name'   => $b->package->name ?? 'Event',
                'client' => $b->client_name,
                'date'   => $b->event_date->format('d M Y'),
                'status' => $b->status,
                'price'  => $b->total_price,
                'url'    => \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $b->id]),
            ]);

        $this->upcomingBookings = $upcomingTours
            ->concat($upcomingRentals)
            ->concat($upcomingEvents)
            ->sortBy('date')
            ->take(10)
            ->values();

        if ($this->role === 'ADMIN') {
            $this->totalUsers       = User::count();
            $this->totalProperties  = Property::count();
            $this->totalAgencies    = Agency::count();
            $this->pendingProperties = Property::where('status', 'DRAFT')->count();
            $this->properties       = Property::latest()->take(5)->get();

            // Booking stats across all 3 types
            $tourRevenue    = TourBooking::whereIn('status', ['CONFIRMED','IN_PROGRESS','COMPLETED'])->sum('total_price');
            $rentalRevenue  = RentalBooking::whereIn('status', ['CONFIRMED','IN_PROGRESS','COMPLETED'])->sum('total_price');
            $eventRevenue   = Booking::whereIn('status', ['CONFIRMED','IN_PROGRESS','COMPLETED'])->sum('total_price');
            $this->totalRevenue = $tourRevenue + $rentalRevenue + $eventRevenue;

            $this->totalBookings = TourBooking::count() + RentalBooking::count() + Booking::count();

            $this->pendingBookings = TourBooking::where('status', 'INQUIRY')->count()
                + RentalBooking::where('status', 'INQUIRY')->count()
                + Booking::where('status', 'INQUIRY')->count();

            $todayTours = TourBooking::with('tour')
                ->whereDate('tour_date', today())
                ->whereIn('status', ['CONFIRMED', 'IN_PROGRESS'])
                ->get()
                ->map(fn($b) => [
                    'icon'   => '🗺️',
                    'name'   => $b->tour->name ?? 'Tour',
                    'client' => $b->client_name,
                    'time'   => 'All day',
                    'status' => $b->status,
                    'color'  => '#0f766e',
                    'url'    => \App\Filament\Resources\TourBookingResource::getUrl('edit', ['record' => $b->id]),
                ]);

            $todayRentals = RentalBooking::with('rentalVehicle')
                ->where('start_date', '<=', today())
                ->where('end_date', '>=', today())
                ->whereIn('status', ['CONFIRMED', 'IN_PROGRESS'])
                ->get()
                ->map(fn($b) => [
                    'icon'   => '🚗',
                    'name'   => $b->rentalVehicle->name ?? 'Vehicle',
                    'client' => $b->client_name,
                    'time'   => $b->start_date->format('d M') . ' → ' . $b->end_date->format('d M'),
                    'status' => $b->status,
                    'color'  => '#1d4ed8',
                    'url'    => \App\Filament\Resources\RentalBookingResource::getUrl('edit', ['record' => $b->id]),
                ]);

            $todayEvents = Booking::with('package')
                ->whereDate('event_date', today())
                ->whereIn('status', ['CONFIRMED', 'IN_PROGRESS'])
                ->get()
                ->map(fn($b) => [
                    'icon'   => '🎉',
                    'name'   => $b->package->name ?? 'Event',
                    'client' => $b->client_name,
                    'time'   => 'All day',
                    'status' => $b->status,
                    'color'  => '#b45309',
                    'url'    => \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $b->id]),
                ]);

            $this->todaySchedule = $todayTours
                ->concat($todayRentals)
                ->concat($todayEvents)
                ->values();

        } else {
            $this->totalProperties = Property::where('user_id', $this->user->id)->count();
            $this->totalViews      = Property::where('user_id', $this->user->id)->sum('views');
            $this->recentLeads     = Inquiry::whereHas('property', function($q) {
                $q->where('user_id', $this->user->id);
            })->latest()->take(5)->get();
            $this->properties      = Property::where('user_id', $this->user->id)->latest()->take(5)->get();

            $this->totalRevenue   = 0;
            $this->totalBookings  = 0;
            $this->pendingBookings = 0;
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MasterCalendarWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}