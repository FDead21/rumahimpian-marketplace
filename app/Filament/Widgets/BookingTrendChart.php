<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TourBooking;
use App\Models\Booking;
use App\Models\RentalBooking;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BookingTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Booking Trend This Year';
    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    protected function getData(): array
    {
        $tours = Trend::model(TourBooking::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        $rentals = Trend::model(RentalBooking::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        $events = Trend::model(Booking::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label'       => 'Tours',
                    'data'        => $tours->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#0f766e',
                    'fill'        => false,
                    'tension'     => 0.4,
                ],
                [
                    'label'       => 'Rentals',
                    'data'        => $rentals->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#1d4ed8',
                    'fill'        => false,
                    'tension'     => 0.4,
                ],
                [
                    'label'       => 'Events',
                    'data'        => $events->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#b45309',
                    'fill'        => false,
                    'tension'     => 0.4,
                ],
            ],
            'labels' => $tours->map(fn(TrendValue $v) => $v->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}