<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model; 

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 1;

    // Show a red badge for NEW inquiries
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('status', 'NEW')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

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
                Forms\Components\Group::make()->schema([
                    // Section 1: Lead Details (Read Only)
                    Forms\Components\Section::make('Lead Information')
                        ->schema([
                            Forms\Components\Placeholder::make('property_name')
                                ->label('Property Interested In')
                                ->content(fn ($record) => $record->property->title ?? 'Unknown'),
                                
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Received At')
                                ->content(fn ($record) => $record->created_at->format('d M Y, H:i')),

                            Forms\Components\Textarea::make('message')
                                ->rows(4)
                                ->disabled()
                                ->columnSpanFull(),
                        ])->columns(2),

                    // Section 2: Buyer Details (Read Only)
                    Forms\Components\Section::make('Buyer Details')
                        ->schema([
                            Forms\Components\TextInput::make('buyer_name')
                                ->label('Name')
                                ->disabled(),
                                
                            Forms\Components\TextInput::make('buyer_phone')
                                ->label('WhatsApp / Phone')
                                ->prefixIcon('heroicon-m-phone')
                                ->disabled(),
                        ])->columns(2),
                ])->columnSpan(2),

                // Section 3: CRM Actions (Editable)
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Manage Status')->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'NEW' => 'New Lead',
                                'CONTACTED' => 'Contacted',
                                'CLOSED' => 'Closed',
                            ])
                            ->required()
                            ->selectablePlaceholder(false)
                            ->native(false),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Internal Notes')
                            ->placeholder('e.g. Called them, waiting for visit...')
                            ->rows(4),
                    ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('buyer_name')
                    ->label('Buyer')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->limit(20)
                    ->url(fn (Inquiry $record) => route('property.show', [$record->property->id, $record->property->slug]))
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'NEW',
                        'warning' => 'CONTACTED',
                        'success' => 'CLOSED',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'NEW' => 'New',
                        'CONTACTED' => 'Contacted',
                        'CLOSED' => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // The WhatsApp Action Button
                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat')
                    ->icon('heroicon-o-chat-bubble-oval-left')
                    ->color('success')
                    ->url(fn (Inquiry $record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->buyer_phone) . "?text=Hello {$record->buyer_name}, this is about your inquiry for {$record->property->title}...", true),
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
            'index' => Pages\ListInquiries::route('/'),
            // 'create' => Pages\CreateInquiry::route('/create'), // Disable create page entirely
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
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