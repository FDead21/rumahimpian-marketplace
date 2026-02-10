<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankResource\Pages;
use App\Models\Bank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bank Identity')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('code')->required(),
                    Forms\Components\FileUpload::make('logo')->directory('banks')->image(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                ])->columns(2),

                Forms\Components\Section::make('KPR Program (Simulation Data)')->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('fixed_rate_1y')->label('Fixed 1Yr (%)')->numeric()->suffix('%'),
                        Forms\Components\TextInput::make('fixed_rate_3y')->label('Fixed 3Yr (%)')->numeric()->suffix('%'),
                        Forms\Components\TextInput::make('fixed_rate_5y')->label('Fixed 5Yr (%)')->numeric()->suffix('%'),
                    ]),
                    
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('floating_rate')
                            ->label('Floating Rate (After Fixed)')
                            ->numeric()->suffix('%')->required()->default(11),
                            
                        Forms\Components\TextInput::make('min_dp_percent')
                            ->label('Min DP (%)')
                            ->numeric()->suffix('%')->required()->default(10),
                            
                        Forms\Components\TextInput::make('max_tenor')
                            ->label('Max Tenor (Years)')
                            ->numeric()->suffix('Years')->required()->default(20),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->height(40),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->badge(),
                Tables\Columns\TextColumn::make('interest_rate')->suffix('%')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBank::route('/create'),
            'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }
    
    // RESTRICT: Only ADMIN can access
    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }
}