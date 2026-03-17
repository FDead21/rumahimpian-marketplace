<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\GalleryEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GalleryResource extends Resource
{
    protected static ?string $model = GalleryEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $navigationLabel = 'Gallery';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['ADMIN', 'EO_AGENT']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Event Info')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($set, $state, $operation) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('event_type')
                        ->options([
                            'Wedding'   => 'Wedding',
                            'Corporate' => 'Corporate',
                            'Birthday'  => 'Birthday',
                            'Gathering' => 'Gathering',
                            'Other'     => 'Other',
                        ])
                        ->native(false),

                    Forms\Components\DatePicker::make('event_date'),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Gallery Photos')->schema([
                    Forms\Components\Repeater::make('media')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('file_path')
                                ->label('Photo')
                                ->image()
                                ->directory('gallery')
                                ->required()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('caption')
                                ->placeholder('Optional caption'),
                            Forms\Components\TextInput::make('sort_order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->reorderableWithButtons()
                        ->orderColumn('sort_order')
                        ->addActionLabel('Add Photo'),
                ]),
            ])->columnSpan(2),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Cover & Publishing')->schema([
                    Forms\Components\FileUpload::make('cover_photo')
                        ->image()
                        ->directory('gallery/covers')
                        ->label('Cover Photo'),

                    Forms\Components\Toggle::make('is_published')
                        ->label('Published (visible on website)')
                        ->default(false),
                ]),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_photo')->label('Cover'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('event_type')->badge(),
                Tables\Columns\TextColumn::make('event_date')->date('d M Y')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Published'),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->options([
                        'Wedding'   => 'Wedding',
                        'Corporate' => 'Corporate',
                        'Birthday'  => 'Birthday',
                        'Gathering' => 'Gathering',
                        'Other'     => 'Other',
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
            'index'  => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit'   => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
