<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyMediaResource\Pages;
use App\Filament\Resources\PropertyMediaResource\RelationManagers;
use App\Models\PropertyMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyMediaResource extends Resource
{
    protected static ?string $model = PropertyMedia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('property_id')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('file_path')
                    ->image() 
                    ->maxSize(5120) 
                    ->directory('properties') 
                    ->required(),
                Forms\Components\TextInput::make('file_type')
                    ->required(),
                Forms\Components\TextInput::make('room_name')
                    ->nullable()
                    ->placeholder('e.g. Living Room, Master Bedroom')
                    ->visible(fn ($get) => $get('file_type') === 'VIRTUAL_TOUR_360'),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_type'),
                Tables\Columns\TextColumn::make('room_name')->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPropertyMedia::route('/'),
            'create' => Pages\CreatePropertyMedia::route('/create'),
            'edit' => Pages\EditPropertyMedia::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }
}
