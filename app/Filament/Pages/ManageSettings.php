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
use Illuminate\Support\Facades\Storage;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $title = 'Website Configuration';
    protected static ?int $navigationSort = 100;
    protected static string $view = 'filament.pages.manage-settings';

    // Only Admins can see this
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public ?array $data = [];

    public function mount(): void
    {
        // Load existing settings into the form
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Information')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Website Logo')
                            ->image()
                            ->directory('settings') 
                            ->preserveFilenames(),
                            
                        FileUpload::make('site_favicon')
                            ->label('Website Favicon (Icon)')
                            ->image()
                            ->directory('settings')
                            ->preserveFilenames(),
                        // ----------------------------

                        TextInput::make('site_name')
                            ->label('Website Name')
                            ->required(),
                        Textarea::make('site_description')
                            ->label('Footer Description')
                            ->rows(3),
                    ]),

                Section::make('Home Page Banner')
                    ->schema([
                        FileUpload::make('hero_slides')
                            ->label('Hero Carousel Images')
                            ->multiple() 
                            ->image()
                            ->directory('hero')
                            ->reorderable()
                            ->preserveFilenames(),
                            
                        TextInput::make('hero_title')
                            ->label('Hero Title')
                            ->default('Find Your Dream Home'),
                        
                        TextInput::make('hero_subtitle')
                            ->label('Hero Subtitle')
                            ->default('Trusted by millions of buyers & agents.'),
                    ]),

                Section::make('Contact Details')
                    ->schema([
                        TextInput::make('contact_phone')->tel(),
                        TextInput::make('contact_email')->email(),
                        TextInput::make('contact_address'),
                    ])->columns(2),

                Section::make('Social Media')
                    ->schema([
                        TextInput::make('social_facebook')->prefix('facebook.com/'),
                        TextInput::make('social_instagram')->prefix('instagram.com/'),
                        TextInput::make('social_twitter')->prefix('x.com/'),
                        TextInput::make('social_youtube')->prefix('youtube.com/'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }
        
        \Illuminate\Support\Facades\Cache::forget('site_settings'); 

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}