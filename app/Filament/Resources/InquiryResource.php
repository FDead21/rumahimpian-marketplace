<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Filament\Resources\InquiryResource\RelationManagers;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; 

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    
    public static function canCreate(): bool
    {
        return false; 
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section 1: The Context
                Forms\Components\Section::make('Lead Details')
                    ->schema([
                        Forms\Components\Placeholder::make('property_name')
                            ->label('Property Interested In')
                            ->content(fn ($record) => $record->property->title),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Received At')
                            ->content(fn ($record) => $record->created_at->format('d M Y, H:i')),
                    ])->columns(2),

                // Section 2: The Buyer
                Forms\Components\Section::make('Buyer Information')
                    ->schema([
                        Forms\Components\TextInput::make('buyer_name')
                            ->label('Name')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('buyer_phone')
                            ->label('WhatsApp / Phone')
                            ->prefixIcon('heroicon-m-phone')
                            ->disabled(), // Tip: You can't click to call in an input, but you can copy it.
                            
                        Forms\Components\TextInput::make('buyer_email')
                            ->label('Email')
                            ->email()
                            ->disabled(),
                    ])->columns(3),

                // Section 3: The Message
                Forms\Components\Section::make('Message Content')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')->label('Property')->limit(20),
                Tables\Columns\TextColumn::make('buyer_name')->label('Buyer')->searchable(),
                Tables\Columns\TextColumn::make('buyer_phone')->label('Phone')->copyable(), 
                Tables\Columns\TextColumn::make('created_at')->since()->label('Received'),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListInquiries::route('/'),
            'create' => Pages\CreateInquiry::route('/create'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
        
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'ADMIN') {
            return $query;
        }

        return $query->whereHas('property', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

}
