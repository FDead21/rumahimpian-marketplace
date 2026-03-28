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
            Forms\Components\Tabs::make('Vendor Information')
                ->tabs([
                    // TAB 1: General Info & Location
                    Forms\Components\Tabs\Tab::make('General Profile')->schema([
                        Forms\Components\Grid::make(2)->schema([
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
                            Forms\Components\TextInput::make('city')
                                ->label('City / Region')
                                ->placeholder('e.g., Bandung'),
                            Forms\Components\TagsInput::make('features')
                                ->label('Features & Tags')
                                ->placeholder('e.g., Halal, Acoustic, Outdoor')
                                ->separator(','),
                        ]),
                        Forms\Components\Textarea::make('address')
                            ->label('Full Address')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Short Summary')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('A quick 1-2 sentence summary for lists.'),
                        
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\FileUpload::make('logo')
                                ->label('Logo / Profile Photo')
                                ->image()
                                ->avatar()
                                ->directory('vendors'),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),

                    // TAB 2: Deep Dive Details
                    Forms\Components\Tabs\Tab::make('Detailed Info & Socials')->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('instagram_url')
                                ->label('Instagram URL')
                                ->url()
                                ->prefix('https://'),
                            Forms\Components\TextInput::make('website_url')
                                ->label('Website URL')
                                ->url()
                                ->prefix('https://'),
                            Forms\Components\TextInput::make('youtube_url')
                                ->label('YouTube Link (Promo Video)')
                                ->url()
                                ->prefix('https://'),
                        ]),
                        Forms\Components\RichEditor::make('detailed_description')
                            ->label('Full Detailed Description')
                            ->toolbarButtons([
                                'bold', 'italic', 'strike', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo',
                            ])
                            ->columnSpanFull()
                            ->helperText('Use this for full service details, history, and terms.'),
                    ]),

                    // TAB 3: Pricing & Service Menu
                    Forms\Components\Tabs\Tab::make('Pricing & Menu')->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('price_from')
                                ->label('General Price From')
                                ->numeric()
                                ->prefix('Rp'),
                            Forms\Components\TextInput::make('price_to')
                                ->label('General Price To')
                                ->numeric()
                                ->prefix('Rp'),
                        ]),
                        
                        Forms\Components\Repeater::make('service_menu')
                            ->label('Specific Item / Service Menu')
                            ->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('item_name')
                                        ->label('Item/Service Name')
                                        ->required()
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Price')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->required(),
                                ]),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Item Photo (Optional)')
                                    ->image()
                                    ->directory('vendors/menu_items')
                                    ->imageEditor(),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['item_name'] ?? null)
                            ->collapsible()
                            ->addActionLabel('Add Service/Item to Menu')
                            ->columnSpanFull(),
                    ]),

                    // TAB 4: Media Gallery (Existing)
                    Forms\Components\Tabs\Tab::make('Portfolio Photos')->schema([
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
                ])
                ->columnSpanFull(),
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
