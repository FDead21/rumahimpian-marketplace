<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Input raw numbers only (e.g. 5500000000)'),
                Forms\Components\Select::make('listing_type')
                    ->options([
                        'SALE' => 'Sale',
                        'RENT' => 'Rent',
                    ])
                    ->required(),
                Forms\Components\Select::make('category')
                    ->options([
                        'RESIDENTIAL' => 'Residential',
                        'COMMERCIAL' => 'Commercial',
                        'LAND' => 'Land',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('property_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('district')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->required(),
                    ])->columns(2),
                Forms\Components\Placeholder::make('helper')
                    ->label('How to get coordinates?')
                    ->content(new \Illuminate\Support\HtmlString('
                        <a href="https://www.google.com/maps" target="_blank" class="text-indigo-600 underline">
                            Open Google Maps
                        </a>. Right-click on a location and select the numbers (e.g., -6.917, 107.619) to copy them.
                    ')),
                Forms\Components\TextInput::make('bedrooms')
                    ->numeric(),
                Forms\Components\TextInput::make('bathrooms')
                    ->numeric(),
                Forms\Components\TextInput::make('land_area')
                    ->numeric(),
                Forms\Components\TextInput::make('building_area')
                    ->numeric(),
                Forms\Components\TextInput::make('specifications'),
                Forms\Components\Select::make('status')
                    ->options([
                        'DRAFT' => 'Draft',
                        'PUBLISHED' => 'Published',
                        'SOLD' => 'Sold',
                    ])
                    ->default('DRAFT')
                    ->required(),
                KeyValue::make('specifications')
                    ->keyLabel('Feature Name')  
                    ->valueLabel('Value')       
                    ->reorderable(),  
                
                Repeater::make('media')
                    ->relationship()
                    ->schema([
                        // The Image Uploader
                        FileUpload::make('file_path')
                            ->label('File')
                            ->image()
                            ->directory('properties')
                            ->required()
                            ->columnSpan(2), // Make image take full width
                        
                        // The "Type" Selector (Fixes the SQL Hack)
                        Forms\Components\Select::make('file_type')
                            ->options([
                                'IMAGE' => 'Standard Image',
                                'VIRTUAL_TOUR_360' => '360° Panorama',
                            ])
                            ->default('IMAGE')
                            ->required(),

                        // Sort Order (Manual override)
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                    ])
                    ->columns(3) // Arrange the fields in a grid inside the repeater
                    ->orderColumn('sort_order')
                    ->reorderableWithButtons()
                    ->grid(2) // Display 2 items per row
                    ->itemLabel(fn (array $state): ?string => $state['file_type'] ?? null), // Shows label on collapsed items

                    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR', locale: 'id_ID') 
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing_type'),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('property_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bedrooms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bathrooms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('land_area')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building_area')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('views')
                    ->icon('heroicon-o-eye')
                    ->numeric()
                    ->sortable()
                    ->label('Total Views'),
                    ])
                    ->filters([
                        //
                    ])
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'ADMIN') {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }
}
