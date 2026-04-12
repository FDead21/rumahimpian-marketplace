<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;
use App\Models\TourBooking;
use App\Models\Booking;
use App\Models\RentalBooking;
use App\Models\AdminCalendarNote;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MasterCalendarWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';
    public string $noteType = 'MEMO';
    public string $noteDescription = '';
    public string $noteScope = 'ALL';

    public ?string $clickedEventId = null;
    public ?string $clickedDate = null;
    public function config(): array
    {
        return [
            'displayEventTime' => false,
            'selectable' => true,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,listMonth',
            ],
        ];
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $this->clickedDate = $start;
        $this->mountAction('createNote');
    }

    public function onEventClick(array $event, array $info = []): void
    {
        $this->clickedEventId = $event['id'] ?? null;
        $this->mountAction('viewEvent'); 
    }

    public function saveCalendarNote(): void
    {
        if (empty(trim($this->noteDescription))) {
            \Filament\Notifications\Notification::make()
                ->title('Please enter a description.')
                ->warning()
                ->send();
            return;
        }

        \App\Models\AdminCalendarNote::create([
            'type'        => $this->noteType,
            'scope'       => $this->noteType === 'BLOCK' ? $this->noteScope : 'ALL',
            'description' => $this->noteDescription,
            'date'        => \Carbon\Carbon::parse($this->clickedDate)->format('Y-m-d'),
        ]);

        $this->noteDescription = '';
        $this->noteType = 'MEMO';
        $this->noteScope = 'ALL';

        $this->closeActionModal();
        $this->refreshRecords();

        \Filament\Notifications\Notification::make()
            ->title('Note added!')
            ->success()
            ->send();
    }

    public function setNoteScope(string $scope): void
    {
        $this->noteScope = $scope;
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $events = [];

        // 1. Tours
        $tourBookings = TourBooking::with('tour')->whereBetween('tour_date', [$fetchInfo['start'], $fetchInfo['end']])->get();
        foreach ($tourBookings as $booking) {
            $events[] = EventData::make()->id('tour_' . $booking->id)->title('🗺️ ' . ($booking->tour->name ?? 'Tour') . ' - ' . $booking->client_name)->start($booking->tour_date)->end($booking->tour_date)->backgroundColor($this->getColorForStatus($booking->status))->borderColor('transparent')->toArray();
        }

        // 2. Events
        $eoBookings = Booking::with('package')->whereBetween('event_date', [$fetchInfo['start'], $fetchInfo['end']])->get();
        foreach ($eoBookings as $booking) {
            $events[] = EventData::make()->id('eo_' . $booking->id)->title('🎉 ' . ($booking->package->name ?? 'Event') . ' - ' . $booking->client_name)->start($booking->event_date)->end($booking->event_date)->backgroundColor($this->getColorForStatus($booking->status))->borderColor('transparent')->toArray();
        }

        // 3. Rentals
        $rentalBookings = RentalBooking::with('rentalVehicle')->where(function ($query) use ($fetchInfo) {
            $query->whereBetween('start_date', [$fetchInfo['start'], $fetchInfo['end']])->orWhereBetween('end_date', [$fetchInfo['start'], $fetchInfo['end']]);
        })->get();
        foreach ($rentalBookings as $booking) {
            $events[] = EventData::make()->id('rental_' . $booking->id)->title('🚗 ' . ($booking->rentalVehicle->name ?? 'Vehicle') . ' - ' . $booking->client_name)->start($booking->start_date)->end(Carbon::parse($booking->end_date)->addDay())->backgroundColor($this->getColorForStatus($booking->status))->borderColor('transparent')->toArray();
        }

        // 4. Admin Notes & Blocks
        $notes = AdminCalendarNote::whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])->get();
        foreach ($notes as $note) {
            $isBlock = $note->type === 'BLOCK';

            $scopeLabel = match($note->scope ?? 'ALL') {
                'TOUR'   => '[Tour] ',
                'RENTAL' => '[Rental] ',
                'EVENT'  => '[Event] ',
                default  => '[All] ',
            };

            $scopeColor = match($note->scope ?? 'ALL') {
                'TOUR'   => '#0f766e', // teal
                'RENTAL' => '#1d4ed8', // blue
                'EVENT'  => '#b45309', // amber
                default  => '#475569', // slate
            };

            $events[] = EventData::make()
                ->id('note_' . $note->id)
                ->title(($isBlock ? '🛑 ' . $scopeLabel : '📝 ') . $note->description)
                ->start($note->date)
                ->end($note->date)
                ->backgroundColor($isBlock ? $scopeColor : '#eab308')
                ->borderColor('transparent')
                ->toArray();
        }

        return $events;
    }

    private function getBookingFromClickedId()
    {
        if (!$this->clickedEventId) return null;

        $idData = explode('_', $this->clickedEventId);
        if (count($idData) < 2) return null;

        $type = $idData[0];
        $id = $idData[1];

        return match ($type) {
            'tour'   => TourBooking::find($id),
            'eo'     => Booking::find($id),
            'rental' => RentalBooking::find($id),
            'note'   => AdminCalendarNote::find($id),
            default  => null,
        };
    }

    /**
     * FEATURE 2 & 3: The Empty Click Action
     */
    protected function createNoteAction(): Action
    {
        return Action::make('createNote')
            ->modalHeading(false)
            ->modalDescription(false)
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalContent(function () {
                return view('filament.widgets.calendar-command-center', [
                    'date' => $this->clickedDate 
                        ? \Carbon\Carbon::parse($this->clickedDate)->translatedFormat('l, d F Y')
                        : now()->translatedFormat('l, d F Y'),
                    'noteType' => $this->noteType, // ✅ Add this
                ]);
            });
    }

    protected function viewEventAction(): Action
    {
        return Action::make('viewEvent')
            ->modalHeading('Command Center')
            ->modalSubmitAction(false) 
            ->modalCancelAction(false) 
            ->modalFooterActions([
                
                // Hide these 3 buttons if we clicked an Admin Note
                Action::make('whatsapp')
                    ->label('WhatsApp Client')
                    ->color('success')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->visible(fn () => !($this->getBookingFromClickedId() instanceof AdminCalendarNote))
                    ->url(function () {
                        $booking = $this->getBookingFromClickedId();
                        if (!$booking) return '#';
                        $phone = preg_replace('/[^0-9]/', '', $booking->client_phone);
                        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                        return "https://wa.me/{$phone}?text=" . urlencode("Halo {$booking->client_name}, regarding your recent booking request...");
                    })->openUrlInNewTab(),

                Action::make('mark_confirmed')
                    ->label('Confirm Booking')
                    ->color('info')
                    ->requiresConfirmation() 
                    ->visible(fn () => !($this->getBookingFromClickedId() instanceof AdminCalendarNote))
                    ->action(function () {
                        $booking = $this->getBookingFromClickedId();
                        if ($booking) {
                            $booking->update(['status' => 'CONFIRMED']);
                            $this->refreshRecords(); 
                            \Filament\Notifications\Notification::make()->title('Confirmed!')->success()->send();
                        }
                    }),

                Action::make('mark_cancelled')
                    ->label('Cancel Booking')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn () => !($this->getBookingFromClickedId() instanceof AdminCalendarNote))
                    ->action(function () {
                        $booking = $this->getBookingFromClickedId();
                        if ($booking) {
                            $booking->update(['status' => 'CANCELLED']);
                            $this->refreshRecords(); 
                        }
                    }),

                // NEW ACTION: Delete Note (Only shows up if we clicked a Note)
                Action::make('delete_note')
                    ->label('Delete Note')
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->requiresConfirmation()
                    ->visible(fn () => $this->getBookingFromClickedId() instanceof AdminCalendarNote)
                    ->action(function () {
                        $note = $this->getBookingFromClickedId();
                        if ($note) {
                            $note->delete();
                            $this->refreshRecords();
                            \Filament\Notifications\Notification::make()->title('Note Removed')->success()->send();
                        }
                    }),
            ])
            ->modalContent(function () {
                $booking = $this->getBookingFromClickedId();
                 if (!$booking) return new \Illuminate\Support\HtmlString('<p style="color:#9ca3af;padding:1rem;">Data not found.</p>');

                // Custom Display for Admin Notes
                if ($booking instanceof AdminCalendarNote) {
                    $scopeLabel = match($booking->scope ?? 'ALL') {
                        'TOUR'   => 'Tours only',
                        'RENTAL' => 'Rentals only',
                        'EVENT'  => 'Events only',
                        default  => 'All booking types',
                    };

                    return view('filament.widgets.calendar-preview', [
                        'title'  => ($booking->type === 'BLOCK' ? '🛑 Blocked Date' : '📝 Internal Memo'),
                        'client' => $booking->description,
                        'date'   => $booking->date->format('d M Y'),
                        'status' => $booking->type === 'BLOCK' 
                            ? 'BLOCK — ' . $scopeLabel 
                            : 'MEMO',
                        'price'  => '-',
                    ]);
                }

                // Normal Display for Bookings
                if ($booking instanceof TourBooking) {
                    $title = "🗺️ Tour: " . ($booking->tour->name ?? 'N/A');
                    $date = $booking->tour_date->format('d M Y');
                } elseif ($booking instanceof Booking) {
                    $title = "🎉 Event: " . ($booking->package->name ?? 'N/A');
                    $date = $booking->event_date->format('d M Y');
                } else {
                    $title = "🚗 Rental: " . ($booking->rentalVehicle->name ?? 'N/A');
                    $date = $booking->start_date->format('d M Y') . ' to ' . $booking->end_date->format('d M Y');
                }

                return view('filament.widgets.calendar-preview', [
                    'title'  => $title,
                    'client' => $booking->client_name . " (" . $booking->client_phone . ")",
                    'date'   => $date,
                    'status' => $booking->status,
                    'price'  => number_format($booking->total_price, 0, ',', '.'),
                ]);
            });
    }

    protected function getActions(): array
    {
        return [
            $this->createNoteAction(),
            $this->viewEventAction(),
        ];
    }

    public function resolveRecord(int | string $key): Model { return new TourBooking(); }
    protected function getEditEventAction(): ?Action   { return null; }

    protected function getCreateEventAction(): ?Action
    {
        return null;
    }


    public function setNoteType(string $type): void
    {
        $this->noteType = $type;
    }

    private function getColorForStatus(string $status): string
    {
        return match($status) {
            'CONFIRMED'   => '#10b981',
            'IN_PROGRESS' => '#3b82f6',
            'COMPLETED'   => '#059669',
            'CANCELLED'   => '#ef4444',
            default       => '#f59e0b',
        };
    }
}