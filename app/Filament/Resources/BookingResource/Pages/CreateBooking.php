<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

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