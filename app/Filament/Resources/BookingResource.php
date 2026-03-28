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

                Forms\Components\Section::make('Booking Info')->schema([
                    Forms\Components\TextInput::make('booking_code')
                        ->label('Booking Code')
                        ->disabled()
                        ->placeholder('Auto-generated'),

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
                        ->native(false)
                        ->default('INQUIRY'),

                    Forms\Components\Select::make('payment_status')
                        ->options([
                            'UNPAID'       => 'Unpaid',
                            'DEPOSIT_PAID' => 'Deposit Paid',
                            'FULLY_PAID'   => 'Fully Paid',
                        ])
                        ->required()
                        ->native(false)
                        ->default('UNPAID'),

                    Forms\Components\Select::make('assigned_to')
                        ->label('Assigned EO Agent')
                        ->options(User::whereIn('role', ['ADMIN', 'EO_AGENT'])->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ])->columns(2),

                Forms\Components\Section::make('Client Information')->schema([
                    Forms\Components\TextInput::make('client_name')->required(),
                    Forms\Components\TextInput::make('client_phone')->tel()->required(),
                    Forms\Components\TextInput::make('client_email')->email()->nullable(),
                ])->columns(2),

                Forms\Components\Section::make('Event Details')->schema([
                    Forms\Components\Select::make('event_type')
                        ->options([
                            'Wedding'   => 'Wedding',
                            'Corporate' => 'Corporate',
                            'Birthday'  => 'Birthday',
                            'Gathering' => 'Gathering',
                            'Other'     => 'Other',
                        ])
                        ->native(false)
                        ->required(),

                    Forms\Components\DatePicker::make('event_date')
                        ->required()
                        ->minDate(now()),

                    Forms\Components\TextInput::make('guest_count')
                        ->label('Guest Count')
                        ->numeric()
                        ->suffix('pax'),

                    Forms\Components\Select::make('package_id')
                        ->label('Package')
                        ->options(Package::where('is_active', true)->pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $package = Package::find($state);
                                $set('total_price', $package?->price);
                            }
                        }),

                    Forms\Components\Hidden::make('property_id'),

                    Forms\Components\Placeholder::make('venue_display')
                        ->label('Venue (Property from Marketplace)')
                        ->content(function (Forms\Get $get): \Illuminate\Support\HtmlString {
                            $id = $get('property_id');
                            if (!$id) return new \Illuminate\Support\HtmlString(
                                '<span class="text-gray-400 italic">No venue selected</span>'
                            );
                            $p = Property::with('media')->find($id);
                            if (!$p) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 italic">Not found</span>');
                            
                            $img = $p->media->first() 
                                ? '<img src="'.asset('storage/'.$p->media->first()->file_path).'" class="w-16 h-12 object-cover rounded inline-block mr-2">'
                                : '';
                            return new \Illuminate\Support\HtmlString(
                                $img.'<strong>'.$p->title.'</strong><br><small class="text-gray-500">'.$p->city.', '.$p->district.'</small>'
                            );
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
                        ->extraModalFooterActions([])
                        ->after(fn () => null),
                    ]),

                    Forms\Components\Textarea::make('notes')
                        ->label('Client Special Requests')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

            ])->columnSpan(2),

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Financials')->schema([
                    Forms\Components\TextInput::make('total_price')
                        ->label('Total Price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\TextInput::make('deposit_amount')
                        ->label('Deposit Received')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                ]),

                Forms\Components\Section::make('Vendors Assigned')->schema([
                    Forms\Components\Select::make('vendors')
                        ->label('Assign Vendors')
                        ->relationship('vendors', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->helperText('Select vendors handling this event'),
                ]),

                Forms\Components\Section::make('Internal Notes')->schema([
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('EO Team Notes')
                        ->placeholder('e.g. Client confirmed date, waiting for DP transfer...')
                        ->rows(4),
                ]),

            ])->columnSpan(1),

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('package.name')
                    ->label('Package')
                    ->badge(),

                Tables\Columns\TextColumn::make('event_type')
                    ->badge(),

                Tables\Columns\TextColumn::make('event_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray'    => 'INQUIRY',
                        'info'    => 'CONFIRMED',
                        'warning' => 'DEPOSIT_PAID',
                        'primary' => 'IN_PROGRESS',
                        'success' => 'COMPLETED',
                        'danger'  => 'CANCELLED',
                    ]),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->colors([
                        'danger'  => 'UNPAID',
                        'warning' => 'DEPOSIT_PAID',
                        'success' => 'FULLY_PAID',
                    ]),

                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'INQUIRY'      => 'Inquiry',
                        'CONFIRMED'    => 'Confirmed',
                        'DEPOSIT_PAID' => 'Deposit Paid',
                        'IN_PROGRESS'  => 'In Progress',
                        'COMPLETED'    => 'Completed',
                        'CANCELLED'    => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('event_type')
                    ->options([
                        'Wedding'   => 'Wedding',
                        'Corporate' => 'Corporate',
                        'Birthday'  => 'Birthday',
                        'Gathering' => 'Gathering',
                        'Other'     => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat')
                    ->icon('heroicon-o-chat-bubble-oval-left')
                    ->color('success')
                    ->url(fn (Booking $record) => $record->whatsapp_url, true),
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
