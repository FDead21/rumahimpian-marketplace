<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ManageEoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'EO Site Settings';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.manage-eo-settings';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::where('key', 'like', 'eo_%')
            ->pluck('value', 'key')
            ->toArray();

        // Decode JSON fields (FileUpload multiple returns JSON array)
        $jsonFields = ['eo_hero_slides'];
        foreach ($jsonFields as $field) {
            if (!empty($settings[$field])) {
                $decoded = json_decode($settings[$field], true);
                if (is_array($decoded)) {
                    $settings[$field] = $decoded;
                }
            }
        }

        // FileUpload single fields — wrap in array if it's a plain string
        $singleFileFields = ['eo_site_logo', 'eo_site_favicon'];
        foreach ($singleFileFields as $field) {
            if (!empty($settings[$field]) && is_string($settings[$field])) {
                $settings[$field] = $settings[$field]; // keep as string, Filament handles it
            }
        }

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('General Information')->schema([
                FileUpload::make('eo_site_logo')
                    ->label('EO Website Logo')
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->preserveFilenames()
                    ->visibility('public'),

                FileUpload::make('eo_site_favicon')
                    ->label('EO Favicon')
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->preserveFilenames()
                    ->visibility('public'),

                TextInput::make('eo_site_name')
                    ->label('EO Website Name')
                    ->required(),

                Textarea::make('eo_site_description')
                    ->label('Footer Description')
                    ->rows(3),
            ]),

            Section::make('Hero Banner')->schema([
                FileUpload::make('eo_hero_slides')
                    ->label('Hero Carousel Images')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('eo-hero')
                    ->reorderable()
                    ->preserveFilenames()
                    ->visibility('public'),

                TextInput::make('eo_hero_title')
                    ->label('Hero Title'),

                TextInput::make('eo_hero_subtitle')
                    ->label('Hero Subtitle'),
            ]),

            Section::make('Contact Details')->schema([
                TextInput::make('eo_contact_phone')->tel(),
                TextInput::make('eo_contact_email')->email(),
                TextInput::make('eo_contact_address'),
            ])->columns(2),

            Section::make('Social Media')->schema([
                TextInput::make('eo_social_facebook')->prefix('facebook.com/'),
                TextInput::make('eo_social_instagram')->prefix('instagram.com/'),
                TextInput::make('eo_social_twitter')->prefix('x.com/'),
                TextInput::make('eo_social_youtube')->prefix('youtube.com/'),
            ])->columns(2),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Skip null/empty but still save empty strings to allow clearing
            if ($value === null) continue;

            if (is_array($value)) {
                $value = json_encode(array_values($value));
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('eo_settings');

        Notification::make()
            ->title('EO Settings saved successfully')
            ->success()
            ->send();
    }
}