<?php

namespace App\Filament\Resources\PropertyMediaResource\Pages;

use App\Filament\Resources\PropertyMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyMedia extends EditRecord
{
    protected static string $resource = PropertyMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
