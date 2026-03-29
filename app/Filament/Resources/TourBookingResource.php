<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TourBookingResource\Pages;
use App\Models\TourBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TourBookingResource extends Resource
{
    protected static ?string $model = TourBooking::class;

    protected static ?string $navigationGroup = 'Tour';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Booking Info')->schema([
                Forms\Components\TextInput::make('booking_code')
                    ->disabled(),
                Forms\Components\Select::make('tour_id')
                    ->relationship('tour', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\DatePicker::make('tour_date')
                    ->required(),
                Forms\Components\TextInput::make('participants')
                    ->numeric()
                    ->required()
                    ->minValue(1),
            ])->columns(2),

            Forms\Components\Section::make('Client Info')->schema([
                Forms\Components\TextInput::make('client_name')->required(),
                Forms\Components\TextInput::make('client_phone')->required(),
                Forms\Components\TextInput::make('client_email')->email()->nullable(),
                Forms\Components\Textarea::make('notes')->nullable()->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Payment & Status')->schema([
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
                Forms\Components\TextInput::make('deposit_amount')
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'UNPAID'       => 'Unpaid',
                        'DEPOSIT_PAID' => 'Deposit Paid',
                        'FULLY_PAID'   => 'Fully Paid',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'INQUIRY'     => 'Inquiry',
                        'CONFIRMED'   => 'Confirmed',
                        'IN_PROGRESS' => 'In Progress',
                        'COMPLETED'   => 'Completed',
                        'CANCELLED'   => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')->nullable()->columnSpanFull(),
                Forms\Components\TextInput::make('cancellation_reason')->nullable()->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('tour.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tour_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participants'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'danger'  => 'UNPAID',
                        'warning' => 'DEPOSIT_PAID',
                        'success' => 'FULLY_PAID',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'INQUIRY',
                        'success' => 'CONFIRMED',
                        'info'    => 'IN_PROGRESS',
                        'success' => 'COMPLETED',
                        'danger'  => 'CANCELLED',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'INQUIRY'     => 'Inquiry',
                        'CONFIRMED'   => 'Confirmed',
                        'IN_PROGRESS' => 'In Progress',
                        'COMPLETED'   => 'Completed',
                        'CANCELLED'   => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'UNPAID'       => 'Unpaid',
                        'DEPOSIT_PAID' => 'Deposit Paid',
                        'FULLY_PAID'   => 'Fully Paid',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTourBookings::route('/'),
            'create' => Pages\CreateTourBooking::route('/create'),
            'edit'   => Pages\EditTourBooking::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'TOUR_GUIDE']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'TOUR_GUIDE']);
    }
}