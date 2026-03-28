<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?int    $navigationSort  = 1;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'EO_AGENT']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Sales & Assignment')->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('WhatsApp Contact Person (Owner)')
                        ->relationship('user', 'name', fn ($query) => $query->whereIn('role', ['ADMIN', 'EO_AGENT']))
                        ->default(auth()->id())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('All new WhatsApp inquiries for this package will be sent to this agent.'),
                ]),

            Forms\Components\Section::make('Package Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Enter raw number e.g. 15000000'),

                Forms\Components\TextInput::make('original_price')
                    ->label('Original Price (for discount display)')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Leave empty if no discount. Must be higher than price.')
                    ->nullable(),

                Forms\Components\TextInput::make('max_pax')
                    ->label('Max Guests (pax)')
                    ->numeric()
                    ->suffix('pax'),

                Forms\Components\FileUpload::make('thumbnail')
                    ->label('Main Thumbnail')
                    ->image()
                    ->directory('packages')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Gallery Images')
                ->description('Upload multiple images for the gallery slideshow on the package detail page.')
                ->schema([
                    Forms\Components\Repeater::make('media')
                        ->relationship('media')
                        ->schema([
                            Forms\Components\FileUpload::make('file_path')
                                ->label('Image')
                                ->image()
                                ->directory('packages/gallery')
                                ->required(),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add Gallery Image')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Location')->schema([
                Forms\Components\TextInput::make('address')
                    ->label('Address / Venue Name')
                    ->placeholder('e.g. Anantara Seminyak, Jl. Abimanyu, Bali')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->placeholder('-8.6905')
                    ->helperText('Get from Google Maps (right click → copy coordinates)'),

                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->placeholder('115.1670'),
            ])->columns(2),

            Forms\Components\Section::make('Package Inclusions')->schema([
                Forms\Components\Repeater::make('inclusions')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('item')
                                ->label('Inclusion Name')
                                ->placeholder('e.g. MC Professional')
                                ->required(),
                            Forms\Components\FileUpload::make('image')
                                ->label('Item Photo')
                                ->image()
                                ->directory('packages/inclusions')
                                ->imageEditor(),
                        ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Item Description')
                            ->rows(2)
                            ->placeholder('Describe what makes this inclusion special...')
                            ->columnSpanFull(),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['item'] ?? null)
                    ->collapsible()
                    ->addActionLabel('Add Inclusion')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Repeater::make('packageVendors') 
                            ->relationship('packageVendors')
                            ->schema([
                                Forms\Components\Select::make('vendor_id')
                                    ->label('Select Vendor')
                                    ->options(\App\Models\Vendor::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                    
                                Forms\Components\Toggle::make('is_mandatory')
                                    ->label('Mandatory Vendor?')
                                    ->helperText('If checked, the customer cannot remove this vendor from the package.')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => \App\Models\Vendor::find($state['vendor_id'])?->name ?? 'New Vendor')
                            ->addActionLabel('Add Vendor to Package'),

            Forms\Components\Section::make('Visibility')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('Active (visible on website)')
                    ->default(true),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured (shown on homepage)')
                    ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor')->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_pax')->suffix(' pax')->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit'   => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}