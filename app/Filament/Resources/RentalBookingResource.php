<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalBookingResource\Pages;
use App\Models\RentalBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RentalBookingResource extends Resource
{
    protected static ?string $model = RentalBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Rental';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Booking Details')->schema([
                Forms\Components\Select::make('rental_vehicle_id')
                    ->relationship('rentalVehicle', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->options([
                        'INQUIRY' => 'Inquiry',
                        'CONFIRMED' => 'Confirmed',
                        'IN_PROGRESS' => 'In Progress',
                        'COMPLETED' => 'Completed',
                        'CANCELLED' => 'Cancelled',
                    ])
                    ->required()
                    ->default('INQUIRY'),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->afterOrEqual('start_date'),
            ])->columns(2),

            Forms\Components\Section::make('Client Information')->schema([
                Forms\Components\TextInput::make('client_name')->required(),
                Forms\Components\TextInput::make('client_phone')->tel()->required(),
                Forms\Components\TextInput::make('client_email')->email(),
            ])->columns(3),

            Forms\Components\Section::make('Financials')->schema([
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\TextInput::make('deposit_amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'UNPAID' => 'Unpaid',
                        'PARTIAL' => 'Partial',
                        'PAID' => 'Paid',
                    ])
                    ->default('UNPAID')
                    ->required(),
            ])->columns(3),

            Forms\Components\Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')->label('Client Notes')->columnSpanFull(),
                Forms\Components\Textarea::make('admin_notes')->label('Internal Admin Notes')->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('rentalVehicle.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client_name')->searchable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'INQUIRY',
                        'success' => fn ($state) => in_array($state, ['CONFIRMED', 'COMPLETED']),
                        'info' => 'IN_PROGRESS',
                        'danger' => 'CANCELLED',
                    ]),
                Tables\Columns\TextColumn::make('total_price')->money('IDR')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'INQUIRY' => 'Inquiry',
                        'CONFIRMED' => 'Confirmed',
                        'IN_PROGRESS' => 'In Progress',
                        'COMPLETED' => 'Completed',
                        'CANCELLED' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalBookings::route('/'),
            'create' => Pages\CreateRentalBooking::route('/create'),
            'edit' => Pages\EditRentalBooking::route('/{record}/edit'),
        ];
    }
}