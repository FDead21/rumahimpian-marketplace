<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TourResource\Pages;
use App\Models\Tour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TourResource extends Resource
{
    protected static ?string $model = Tour::class;

    protected static ?string $navigationGroup = 'Tour';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $navigationIcon  = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Basic Info')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        if (!$record?->slug) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->dehydrated(),
                Forms\Components\Select::make('category')
                    ->options([
                        'ADVENTURE'   => '🏔️ Adventure',
                        'CULTURAL'    => '🏛️ Cultural',
                        'NATURE'      => '🌿 Nature',
                        'WATER_SPORTS' => '🌊 Water Sports',
                        'CUSTOM'      => '✏️ Custom',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('custom_category')
                    ->label('Custom Category Name')
                    ->nullable()
                    ->visible(fn ($get) => $get('category') === 'CUSTOM'),
                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\TextInput::make('duration_label')
                    ->nullable()
                    ->placeholder('e.g. 1 Day, 3D2N')
                    ->helperText('Human-readable duration label'),
                Forms\Components\TextInput::make('min_participants')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\TextInput::make('max_participants')
                    ->numeric()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Pricing')->schema([
                Forms\Components\TextInput::make('price_per_person')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
                Forms\Components\TextInput::make('original_price')
                    ->numeric()
                    ->prefix('IDR')
                    ->nullable()
                    ->helperText('Leave empty if no discount'),
            ])->columns(2),

            Forms\Components\Section::make('Meeting Point')->schema([
                Forms\Components\TextInput::make('meeting_point')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('meeting_point_lat')
                    ->label('Latitude')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('meeting_point_lng')
                    ->label('Longitude')
                    ->numeric()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Details')->schema([
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('itinerary')
                    ->label('Itinerary (Day by Day)')
                    ->schema([
                        Forms\Components\TextInput::make('day')
                            ->label('Day')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Day Title')
                            ->placeholder('e.g. Arrival & Ubud Exploration')
                            ->required(),
                        Forms\Components\Repeater::make('items')
                            ->label('Schedule Items')
                            ->schema([
                                Forms\Components\TextInput::make('time')
                                    ->placeholder('e.g. 08:00 AM')
                                    ->nullable(),
                                Forms\Components\TextInput::make('activity')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->rows(2)
                                    ->nullable(),
                            ])
                            ->columns(3)
                            ->defaultItems(1),
                    ])
                    ->columns(2)
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('inclusions')
                    ->label('Inclusions')
                    ->schema([
                        Forms\Components\TextInput::make('item')->required(),
                        Forms\Components\Textarea::make('description')->rows(2)->nullable(),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('tours/inclusions')
                            ->nullable(),
                    ])
                    ->columns(3)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Media')->schema([
                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->directory('tours/thumbnails')
                    ->nullable(),
                Forms\Components\Repeater::make('media')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->image()
                            ->directory('tours/media')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

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
                Tables\Columns\ImageColumn::make('thumbnail')->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'warning' => 'ADVENTURE',
                        'info'    => 'CULTURAL',
                        'success' => 'NATURE',
                        'primary' => 'WATER_SPORTS',
                        'gray'    => 'CUSTOM',
                    ]),
                Tables\Columns\TextColumn::make('duration_label')
                    ->label('Duration')
                    ->default(fn ($record) => $record->duration_days . 'd'),
                Tables\Columns\TextColumn::make('price_per_person')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_participants')
                    ->label('Min Pax'),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Max Pax')
                    ->default('∞'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'ADVENTURE'    => 'Adventure',
                        'CULTURAL'     => 'Cultural',
                        'NATURE'       => 'Nature',
                        'WATER_SPORTS' => 'Water Sports',
                        'CUSTOM'       => 'Custom',
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
            'index'  => Pages\ListTours::route('/'),
            'create' => Pages\CreateTour::route('/create'),
            'edit'   => Pages\EditTour::route('/{record}/edit'),
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