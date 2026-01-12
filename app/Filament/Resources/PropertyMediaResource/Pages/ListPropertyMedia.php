<?php

namespace App\Filament\Resources\PropertyMediaResource\Pages;

use App\Filament\Resources\PropertyMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyMedia extends ListRecords
{
    protected static string $resource = PropertyMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
