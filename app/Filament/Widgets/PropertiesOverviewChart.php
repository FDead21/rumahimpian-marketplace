<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PropertiesOverviewChart extends ChartWidget
{
    protected static ?string $heading = 'Properties Growth';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $query = Property::query();

        if (auth()->user()->role !== 'ADMIN') {
            $query->where('user_id', auth()->id());
        }

        $data = Trend::query($query)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Properties',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4f46e5', // Indigo
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}