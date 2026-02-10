<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyResource\Pages;
use App\Filament\Resources\AgencyResource\RelationManagers;
use App\Models\Agency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class AgencyResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($set, $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                    Forms\Components\TextInput::make('slug')->disabled()->dehydrated(),
                    Forms\Components\FileUpload::make('logo')
                        ->image()
                        ->avatar()
                        ->maxSize(2048) 
                        ->directory('agency-logos'),
                    Forms\Components\TextInput::make('phone'),
                    Forms\Components\Textarea::make('address')->columnSpanFull(),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            // 1. Show the Logo (if you have one)
            ImageColumn::make('logo_url')
                ->label('Logo')
                ->circular(),

            // 2. Show the Agency Name
            TextColumn::make('name')
                ->label('Agency Name')
                ->sortable()
                ->searchable()
                ->weight('bold'),

            // 3. Show the Email
            TextColumn::make('email')
                ->icon('heroicon-m-envelope')
                ->copyable(),

            // 4. Show Phone (Optional)
            TextColumn::make('phone_number')
                ->label('Phone'),
                
            // 5. Show Creation Date
            TextColumn::make('created_at')
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
            'index' => Pages\ListAgencies::route('/'),
            'create' => Pages\CreateAgency::route('/create'),
            'edit' => Pages\EditAgency::route('/{record}/edit'),
        ];
    }
}
