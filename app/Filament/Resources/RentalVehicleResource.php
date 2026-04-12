<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalVehicleResource\Pages;
use App\Models\RentalVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;

class RentalVehicleResource extends Resource
{
    protected static ?string $model = RentalVehicle::class;

    protected static ?string $navigationGroup = 'Rental';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $navigationIcon  = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        if (! $record?->slug) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->dehydrated(),
                Forms\Components\Select::make('vehicle_type')
                    ->options([
                        'CAR'       => 'Car',
                        'MOTORBIKE' => 'Motorbike / Scooter',
                        'BOAT'      => 'Boat',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('brand')->nullable(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('max_passengers')
                    ->numeric()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Pricing & Location')->schema([
                Forms\Components\TextInput::make('price_per_day')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
                Forms\Components\TextInput::make('city')->required(),
                Forms\Components\TextInput::make('address')->nullable(),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()->nullable(),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Details')->schema([
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('specifications')
                    ->nullable()
                    ->columnSpanFull()
                    ->helperText('e.g. Transmission: Automatic, Fuel: Petrol, CC: 1500'),
            ]),

            Forms\Components\Section::make('Media')->schema([
                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->directory('rental/thumbnails')
                    ->nullable(),
                Forms\Components\Repeater::make('media')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->image()
                            ->directory('rental/media')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Manual Availability Override')
                ->description('Block specific dates to prevent users from booking them (e.g., for maintenance or holidays).')
                ->schema([
                    Forms\Components\Repeater::make('blocked_dates')
                        ->label('Blocked Dates')
                        ->simple(
                            Forms\Components\DatePicker::make('date')->native(false)->displayFormat('Y-m-d')->format('Y-m-d')
                        )
                        ->addActionLabel('Add Blocked Date')
                        ->columnSpanFull(),
                ])->collapsed(),

            Forms\Components\Section::make('Visibility')->schema([
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\Toggle::make('is_featured')->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('vehicle_type')
                    ->colors([
                        'primary' => 'CAR',
                        'warning' => 'MOTORBIKE',
                        'info'    => 'BOAT',
                    ]),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_type')
                    ->options([
                        'CAR'       => 'Car',
                        'MOTORBIKE' => 'Motorbike / Scooter',
                        'BOAT'      => 'Boat',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListRentalVehicles::route('/'),
            'create' => Pages\CreateRentalVehicle::route('/create'),
            'edit'   => Pages\EditRentalVehicle::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'VEHICLE_RENTER']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'VEHICLE_RENTER']);
    }
}