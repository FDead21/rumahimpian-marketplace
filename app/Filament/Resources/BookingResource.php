<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Property;
use App\Models\Vendor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int    $navigationSort  = 2;

    // Badge: count INQUIRY status bookings
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('status', 'INQUIRY')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'EO_AGENT']);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Core Status')->schema([
                    Forms\Components\TextInput::make('booking_code')
                        ->label('Booking Code')
                        ->disabled()
                        ->dehydrated(false) // Don't send to DB on update
                        ->columnSpan(1),

                    Forms\Components\Select::make('status')
                        ->options([
                            'INQUIRY'      => 'Inquiry',
                            'CONFIRMED'    => 'Confirmed',
                            'DEPOSIT_PAID' => 'Deposit Paid',
                            'IN_PROGRESS'  => 'In Progress',
                            'COMPLETED'    => 'Completed',
                            'CANCELLED'    => 'Cancelled',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('payment_status')
                        ->options([
                            'UNPAID'       => 'Unpaid',
                            'DEPOSIT_PAID' => 'Deposit Paid',
                            'FULLY_PAID'   => 'Fully Paid',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('assigned_to')
                        ->label('Assigned Agent')
                        ->relationship('assignedTo', 'name', fn (Builder $query) => $query->whereIn('role', ['ADMIN', 'EO_AGENT']))
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])->columns(2),

                Forms\Components\Section::make('Client & Event Details')->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('client_name')->required(),
                        Forms\Components\TextInput::make('client_phone')->tel()->required(),
                        Forms\Components\TextInput::make('client_email')->email(),
                    ]),
                    
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('event_type')
                            ->options(['Wedding' => 'Wedding', 'Corporate' => 'Corporate', 'Birthday' => 'Birthday', 'Gathering' => 'Gathering', 'Other' => 'Other'])
                            ->required(),
                        Forms\Components\DatePicker::make('event_date')->required(),
                        Forms\Components\TextInput::make('guest_count')->numeric()->suffix('pax'),
                    ]),

                    Forms\Components\Select::make('package_id')
                        ->relationship('package', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($state, $set) => $set('total_price', Package::find($state)?->price)),

                    // --- VENUE PICKER RE-INSTATED ---
                    Forms\Components\Hidden::make('property_id'),

                    Forms\Components\Placeholder::make('venue_display')
                        ->label('Venue (Property from Marketplace)')
                        ->content(function (Forms\Get $get): \Illuminate\Support\HtmlString {
                            $id = $get('property_id');
                            if (!$id) return new \Illuminate\Support\HtmlString(
                                '<div class="flex items-center gap-3 p-3 rounded-lg border border-dashed border-gray-600 text-gray-400 italic text-sm">No venue selected</div>'
                            );
                            $p = Property::with('media')->find($id);
                            if (!$p) return new \Illuminate\Support\HtmlString(
                                '<div class="text-gray-400 italic text-sm">Not found</div>'
                            );

                            $imgSrc = $p->media->first()
                                ? asset('storage/' . $p->media->first()->file_path)
                                : null;

                            $imgHtml = $imgSrc
                                ? '<img src="' . $imgSrc . '" class="w-full h-40 object-cover rounded-lg">'
                                : '<div class="w-full h-40 bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 text-sm">No Image</div>';

                            return new \Illuminate\Support\HtmlString('
                                <div class="rounded-xl border border-gray-700 overflow-hidden">
                                    ' . $imgHtml . '
                                    <div class="p-3">
                                        <p class="font-semibold text-white text-sm">' . e($p->title) . '</p>
                                        <p class="text-gray-400 text-xs mt-0.5">' . e($p->city) . ', ' . e($p->district) . '</p>
                                    </div>
                                </div>
                            ');
                        })
                        ->live()
                        ->extraAttributes(['id' => 'venue-display-placeholder']),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('open_venue_picker')
                            ->label('Browse & Select Venue')
                            ->icon('heroicon-o-building-office-2')
                            ->color('primary')
                            ->modalHeading('Select a Venue')
                            ->modalWidth('6xl')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Done')
                            ->modalContent(fn () => view('filament.modals.venue-picker'))
                    ]),
                    // --------------------------------

                    Forms\Components\Textarea::make('notes')
                        ->label('Customer Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

                // --- CRITICAL UPGRADE: Vendor Pivot Repeater ---
                Forms\Components\Repeater::make('bookingVendors') // <-- Changed this
                            ->relationship('bookingVendors') // <-- Point to the hasMany
                            ->schema([
                                Forms\Components\Select::make('vendor_id')
                                    ->label('Vendor')
                                    ->options(\App\Models\Vendor::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                
                                Forms\Components\TextInput::make('agreed_price')
                                    ->label('Agreed Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                
                                Forms\Components\Textarea::make('notes')
                                    ->label('Service Details')
                                    ->placeholder('e.g. 1 Night Rock performance')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => \App\Models\Vendor::find($state['vendor_id'])?->name ?? 'New Vendor')
                            ->collapsible()
                            ->grid(1)
                            ->addActionLabel('Add Vendor Add-on'),

            ])->columnSpan(2),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Financials')->schema([
                    Forms\Components\TextInput::make('total_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->helperText('Grand total including package and all vendors'),

                    Forms\Components\TextInput::make('deposit_amount')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                ]),

                Forms\Components\Section::make('Internal Administration')->schema([
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Private EO Notes')
                        ->placeholder('Internal team communication only...')
                        ->rows(6),
                ]),

                Forms\Components\Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn ($record): string => $record?->created_at ? $record->created_at->diffForHumans() : '-'),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Event Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'INQUIRY'      => 'Inquiry',
                        'CONFIRMED'    => 'Confirmed',
                        'DEPOSIT_PAID' => 'Deposit Paid',
                        'IN_PROGRESS'  => 'In Progress',
                        'COMPLETED'    => 'Completed',
                        'CANCELLED'    => 'Cancelled',
                    ]),

                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR', locale: 'id_ID')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('IDR', locale: 'id_ID')),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Agent')
                    ->placeholder('Unassigned'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // --- NEW ACTION: Open Receipt ---
                Tables\Actions\Action::make('view_receipt')
                    ->label('Receipt')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn (Booking $record) => route('eventOrganizer.booking.confirmation', $record->booking_code))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (Booking $record) => $record->whatsapp_url)
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // EO_AGENT only sees bookings assigned to them
        if (auth()->user()->role === 'EO_AGENT') {
            return $query->where('assigned_to', auth()->id());
        }

        return $query; // ADMIN sees all
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit'   => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
