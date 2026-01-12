<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PropertyViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Most Popular Properties';
    
    // Make it span the full width
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get Top 5 properties by views
        $data = Property::orderByDesc('views')->take(5)->get();

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $data->pluck('views'),
                    'backgroundColor' => '#4F46E5', // Indigo color
                ],
            ],
            'labels' => $data->pluck('title'),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart looks best for ranking
    }

    public static function canView(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }
}