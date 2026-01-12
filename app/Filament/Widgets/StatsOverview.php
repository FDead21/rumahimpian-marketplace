<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Properties', Property::count())
                ->description('Active listings in database')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),

            Stat::make('Total Agents', User::where('role', 'AGENT')->count())
                ->description('Registered agents')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Page Views', Property::sum('views'))
                ->description('Across all listings')
                ->descriptionIcon('heroicon-m-eye')
                ->color('warning'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }
}