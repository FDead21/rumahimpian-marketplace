<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertyTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Properties by Type';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $types = Property::where('user_id', auth()->id())
            ->select('property_type', DB::raw('count(*) as total'))
            ->groupBy('property_type')
            ->pluck('total', 'property_type')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'My Property Types',
                    'data' => array_values($types),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    ],
                ],
            ],
            'labels' => array_keys($types),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}