<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TourBooking;
use App\Models\Booking;
use App\Models\RentalBooking;

class RevenueByTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue by Booking Type';
    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    protected function getData(): array
    {
        $tourRevenue   = TourBooking::whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])->sum('total_price');
        $rentalRevenue = RentalBooking::whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])->sum('total_price');
        $eventRevenue  = Booking::whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])->sum('total_price');

        return [
            'datasets' => [
                [
                    'data'            => [$tourRevenue, $rentalRevenue, $eventRevenue],
                    'backgroundColor' => ['#0f766e', '#1d4ed8', '#b45309'],
                    'borderWidth'     => 0,
                ],
            ],
            'labels' => ['Tours', 'Rentals', 'Events'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}