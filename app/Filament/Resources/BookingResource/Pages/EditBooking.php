<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getListeners(): array
    {
        return [
            'venue-selected' => 'handleVenueSelected',
        ];
    }

    public function handleVenueSelected(int $id, string $label): void
    {
        $this->data['property_id'] = $id;
    }
}