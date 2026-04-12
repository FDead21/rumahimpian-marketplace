<div x-data="{ type: 'MEMO' }">

    {{-- Header --}}
    <div style="padding-bottom: 1rem; border-bottom: 1px solid #374151; margin-bottom: 1rem;">
        <p style="font-size: 1.1rem; font-weight: 600; color: white; margin: 0;">Command center</p>
        <p style="font-size: 0.85rem; color: #9ca3af; margin: 4px 0 0;">{{ $date }}</p>
    </div>

    {{-- Note Section --}}
    <div style="padding-bottom: 1rem; border-bottom: 1px solid #374151; margin-bottom: 1rem;">
        <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin: 0 0 0.75rem;">
            Add a note for this date
        </p>

        {{-- Type Toggle --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 4px; background: #1f2937; border-radius: 8px; margin-bottom: 12px;">
            <button
                type="button"
                @click="type = 'MEMO'; $wire.setNoteType('MEMO')"
                :style="type === 'MEMO'
                    ? 'background: white; color: #111827; padding: 8px; border-radius: 6px; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;'
                    : 'background: transparent; color: #9ca3af; padding: 8px; border-radius: 6px; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;'"
            >
                Internal memo
            </button>
            <button
                type="button"
                @click="type = 'BLOCK'; $wire.setNoteType('BLOCK')"
                :style="type === 'BLOCK'
                    ? 'background: white; color: #111827; padding: 8px; border-radius: 6px; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;'
                    : 'background: transparent; color: #9ca3af; padding: 8px; border-radius: 6px; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;'"
            >
                Block date
            </button>
        </div>

        {{-- Scope Selector — only shows for BLOCK --}}
        <div x-show="type === 'BLOCK'"
             style="margin-bottom: 12px;">
            <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin: 0 0 6px;">
                Block applies to
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                <template x-for="(label, value) in {ALL: '🌐 All bookings', TOUR: '🗺️ Tours only', RENTAL: '🚗 Rentals only', EVENT: '🎉 Events only'}" :key="value">
                    <button
                        type="button"
                        @click="$wire.setNoteScope(value)"
                        :style="$wire.noteScope === value
                            ? 'background: #2563eb; color: white; padding: 7px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; border: none; cursor: pointer;'
                            : 'background: #1f2937; color: #9ca3af; padding: 7px 8px; border-radius: 6px; font-size: 0.8rem; border: 1px solid #374151; cursor: pointer;'"
                        x-text="label"
                    ></button>
                </template>
            </div>
        </div>

        {{-- Input --}}
        <input
            type="text"
            wire:model="noteDescription"
            placeholder="e.g. National holiday, fleet maintenance..."
            style="width: 100%; box-sizing: border-box; padding: 8px 12px; border-radius: 8px; font-size: 0.875rem; background: #111827; border: 1px solid #374151; color: white; margin-bottom: 12px; outline: none;"
        />

        {{-- Save --}}
        <button
            type="button"
            wire:click="saveCalendarNote"
            style="width: 100%; padding: 9px; border-radius: 8px; background: #2563eb; color: white; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer;"
        >
            Save note
        </button>
    </div>

    {{-- Booking Shortcuts --}}
    <div>
        <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin: 0 0 0.75rem;">
            Create a new booking
        </p>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ \App\Filament\Resources\RentalVehicleResource::getUrl('create') }}"
               style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; border: 1px solid #374151; text-decoration: none; color: #e5e7eb;"
               onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='transparent'">
                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(59,130,246,0.2); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">🚗</span>
                <span style="flex: 1; font-size: 0.875rem;">New rental booking</span>
                <span style="color: #6b7280; font-size: 0.875rem;">→</span>
            </a>
            <a href="{{ \App\Filament\Resources\TourResource::getUrl('create') }}"
               style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; border: 1px solid #374151; text-decoration: none; color: #e5e7eb;"
               onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='transparent'">
                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(34,197,94,0.2); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">🗺️</span>
                <span style="flex: 1; font-size: 0.875rem;">New tour booking</span>
                <span style="color: #6b7280; font-size: 0.875rem;">→</span>
            </a>
            <a href="{{ \App\Filament\Resources\PackageResource::getUrl('create') }}"
               style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; border: 1px solid #374151; text-decoration: none; color: #e5e7eb;"
               onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='transparent'">
                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(245,158,11,0.2); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">🎉</span>
                <span style="flex: 1; font-size: 0.875rem;">New event booking</span>
                <span style="color: #6b7280; font-size: 0.875rem;">→</span>
            </a>
        </div>
    </div>
</div>