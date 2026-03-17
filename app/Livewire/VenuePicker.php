<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class VenuePicker extends Component
{
    use WithPagination;

    public string $venueSearch = '';
    public string $venueCity = '';
    public string $venueType = '';
    public ?int $selectedPropertyId = null;

    public function updatingVenueSearch(): void { $this->resetPage(); }
    public function updatingVenueCity(): void   { $this->resetPage(); }
    public function updatingVenueType(): void   { $this->resetPage(); }

    public function selectProperty(int $id): void
    {
        $this->selectedPropertyId = $id;
        $property = Property::with('media')->find($id);
        
        $this->dispatch('venue-selected',
            id: $id,
            label: "{$property->title} — {$property->city}, {$property->district}"
        );
    }

    public function render()
    {
        $properties = Property::with('media')
            ->where('status', 'PUBLISHED')
            ->where('listing_type', 'RENT')
            ->when($this->venueSearch, fn ($q) =>
                $q->where(function ($sub) {
                    $sub->where('title', 'like', "%{$this->venueSearch}%")
                        ->orWhere('city', 'like', "%{$this->venueSearch}%")
                        ->orWhere('district', 'like', "%{$this->venueSearch}%")
                        ->orWhere('address', 'like', "%{$this->venueSearch}%");
                })
            )
            ->when($this->venueCity, fn ($q) => $q->where('city', $this->venueCity))
            ->when($this->venueType, fn ($q) => $q->where('property_type', $this->venueType))
            ->paginate(9);

        return view('filament.components.venue-picker-modal', [
            'properties' => $properties,
            'cities' => Property::where('status','PUBLISHED')->where('listing_type','RENT')->distinct()->orderBy('city')->pluck('city'),
            'types'  => Property::where('status','PUBLISHED')->where('listing_type','RENT')->distinct()->orderBy('property_type')->pluck('property_type'),
        ]);
    }
}