<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Article Content')->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                    Forms\Components\Section::make('SEO Settings')->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Meta Title')
                            ->maxLength(60),
                        
                        Forms\Components\Textarea::make('seo_description')
                            ->label('Meta Description')
                            ->maxLength(160),

                        Forms\Components\TextInput::make('seo_keywords')
                            ->placeholder('property, real estate, investment'),
                    ]),
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Publishing')->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->directory('articles'),

                        Forms\Components\Toggle::make('is_published')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->default(now()),
                    ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    // --- ACCESS CONTROL: ADMIN ONLY ---
    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }
}