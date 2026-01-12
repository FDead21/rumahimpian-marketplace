<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Inquiry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AgentStatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()->role === 'AGENT';
    }

    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('My Active Listings', Property::where('user_id', $userId)->where('status', 'PUBLISHED')->count())
                ->description('Properties live on website')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),

            Stat::make('My Total Views', Property::where('user_id', $userId)->sum('views'))
                ->description('Across all your listings')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('My Leads', Inquiry::whereHas('property', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->count())
                ->description('Messages from buyers')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('warning'),
        ];
    }
}