<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?int    $navigationSort  = 2;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'EO_AGENT']);
    }

    public static function canCreate(): bool { return auth()->user()->role === 'ADMIN'; }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return auth()->user()->role === 'ADMIN'; }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return auth()->user()->role === 'ADMIN'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vendor Details')->schema([
                Forms\Components\TextInput::make('name')->required(),

                Forms\Components\Select::make('category')
                    ->options([
                        'Catering'      => 'Catering',
                        'Decoration'    => 'Decoration',
                        'Photography'   => 'Photography',
                        'Videography'   => 'Videography',
                        'Entertainment' => 'Entertainment (Band/MC)',
                        'Makeup'        => 'Makeup & Salon',
                        'Florist'       => 'Florist',
                        'Transport'     => 'Transport',
                        'Other'         => 'Other',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('phone')->tel(),
                Forms\Components\TextInput::make('email')->email(),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('logo')
                    ->label('Logo / Profile Photo')
                    ->image()
                    ->avatar()
                    ->directory('vendors'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Pricing')->schema([
                Forms\Components\TextInput::make('price_from')
                    ->label('Price From')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('price_to')
                    ->label('Price To')
                    ->numeric()
                    ->prefix('Rp'),
            ])->columns(2),

            Forms\Components\Section::make('Portfolio Photos')->schema([
                Forms\Components\Repeater::make('media')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Photo')
                            ->image()
                            ->directory('vendors/portfolio')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add Photo'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('price_from')
                    ->money('IDR', locale: 'id_ID')
                    ->label('From'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Catering'      => 'Catering',
                        'Decoration'    => 'Decoration',
                        'Photography'   => 'Photography',
                        'Videography'   => 'Videography',
                        'Entertainment' => 'Entertainment',
                        'Makeup'        => 'Makeup',
                        'Florist'       => 'Florist',
                        'Transport'     => 'Transport',
                        'Other'         => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
