<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PropertiesOverviewChart extends ChartWidget
{
    protected static ?string $heading = 'New Properties (Last 6 Months)';
    
    // Sort order: 2 means it appears after the stats cards (if we make them)
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        $query = \App\Models\Property::query()->where('user_id', auth()->id());

        $data = \Flowframe\Trend\Trend::query($query)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();
 
        return [
            'datasets' => [
                [
                    'label' => 'My Listings Growth',
                    'data' => $data->map(fn (\Flowframe\Trend\TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4F46E5', 
                    'fill' => true,
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                ],
            ],
            'labels' => $data->map(fn (\Flowframe\Trend\TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}