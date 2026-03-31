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
use Filament\Forms\Components\Tabs;
use Filament\Notifications\Notification;
use App\Models\Setting;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon    = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $title             = 'Website Configuration';
    protected static string  $view              = 'filament.pages.manage-settings';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Decode JSON fields for FileUpload multiple
        foreach (['hero_slides', 'eo_hero_slides', 'tour_hero_slides', 'rental_hero_slides'] as $field) {
            if (!empty($settings[$field]) && is_string($settings[$field])) {
                $decoded = json_decode($settings[$field], true);
                if (is_array($decoded)) {
                    $settings[$field] = $decoded;
                }
            }
        }

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([

                        // TAB 1: Global
                        Tabs\Tab::make('Global')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Branding')
                                    ->schema([
                                        FileUpload::make('site_logo')
                                            ->label('Website Logo')
                                            ->image()->disk('public')->directory('settings')->preserveFilenames(),
                                        FileUpload::make('site_favicon')
                                            ->label('Favicon')
                                            ->image()->disk('public')->directory('settings')->preserveFilenames(),
                                        TextInput::make('site_name')->label('Website Name')->required(),
                                        Textarea::make('site_description')->label('Footer Description')->rows(3)->columnSpanFull(),
                                    ])->columns(2),
                                Section::make('Contact Details')
                                    ->schema([
                                        TextInput::make('contact_phone')->tel(),
                                        TextInput::make('contact_email')->email(),
                                        TextInput::make('contact_address')->columnSpanFull(),
                                    ])->columns(2),
                                Section::make('Social Media')
                                    ->schema([
                                        TextInput::make('social_facebook')->prefix('facebook.com/'),
                                        TextInput::make('social_instagram')->prefix('instagram.com/'),
                                        TextInput::make('social_twitter')->prefix('x.com/'),
                                        TextInput::make('social_youtube')->prefix('youtube.com/'),
                                    ])->columns(2),
                            ]),

                        // TAB 2: Property
                        Tabs\Tab::make('Property')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make('Hero Banner')
                                    ->schema([
                                        FileUpload::make('hero_slides')->label('Hero Carousel Images')
                                            ->multiple()->image()->disk('public')->directory('hero')->reorderable()->preserveFilenames()->columnSpanFull(),
                                        TextInput::make('hero_title')->label('Hero Title')->placeholder('Find Your Dream Home'),
                                        TextInput::make('hero_subtitle')->label('Hero Subtitle')->placeholder('Trusted by millions of buyers & agents.'),
                                    ])->columns(2),
                            ]),

                        // TAB 3: Event Organizer
                        Tabs\Tab::make('Event Organizer')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Section::make('Hero Banner')
                                    ->schema([
                                        FileUpload::make('eo_hero_slides')->label('EO Hero Carousel Images')
                                            ->multiple()->image()->disk('public')->directory('eo-hero')->reorderable()->preserveFilenames()->columnSpanFull(),
                                        TextInput::make('eo_hero_title')->label('Hero Title')->placeholder('Your Dream Event, Made Real'),
                                        TextInput::make('eo_hero_subtitle')->label('Hero Subtitle')->placeholder('Professional event organizer...'),
                                    ])->columns(2),
                            ]),

                        // TAB 4: Tours
                        Tabs\Tab::make('Tours')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Section::make('Hero Banner')
                                    ->schema([
                                        FileUpload::make('tour_hero_slides')->label('Tour Hero Carousel Images')
                                            ->multiple()->image()->disk('public')->directory('tour-hero')->reorderable()->preserveFilenames()->columnSpanFull(),
                                        TextInput::make('tour_hero_title')->label('Hero Title')->placeholder('Explore Breathtaking Destinations'),
                                        TextInput::make('tour_hero_subtitle')->label('Hero Subtitle')->placeholder('Discover cultural tours, hikes, and adventure packages.'),
                                    ])->columns(2),
                            ]),

                        // TAB 5: Rentals
                        Tabs\Tab::make('Rentals')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Section::make('Hero Banner')
                                    ->schema([
                                        FileUpload::make('rental_hero_slides')->label('Rental Hero Carousel Images')
                                            ->multiple()->image()->disk('public')->directory('rental-hero')->reorderable()->preserveFilenames()->columnSpanFull(),
                                        TextInput::make('rental_hero_title')->label('Hero Title')->placeholder('Rent the Perfect Vehicle for Your Trip'),
                                        TextInput::make('rental_hero_subtitle')->label('Hero Subtitle')->placeholder('Cars, motorbikes, and boats at the best prices.'),
                                    ])->columns(2),
                            ]),

                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $groupMap = [
            'site_logo'         => 'GLOBAL',
            'site_favicon'      => 'GLOBAL',
            'site_name'         => 'GLOBAL',
            'site_description'  => 'GLOBAL',
            'contact_phone'     => 'GLOBAL',
            'contact_email'     => 'GLOBAL',
            'contact_address'   => 'GLOBAL',
            'social_facebook'   => 'GLOBAL',
            'social_instagram'  => 'GLOBAL',
            'social_twitter'    => 'GLOBAL',
            'social_youtube'    => 'GLOBAL',
            
            'hero_slides'       => 'PROPERTY',
            'hero_title'        => 'PROPERTY',
            'hero_subtitle'     => 'PROPERTY',
            
            'eo_hero_slides'    => 'EO',
            'eo_hero_title'     => 'EO',
            'eo_hero_subtitle'  => 'EO',
            
            'tour_hero_slides'   => 'TOUR',
            'tour_hero_title'    => 'TOUR',
            'tour_hero_subtitle' => 'TOUR',
            
            'rental_hero_slides'   => 'RENTAL',
            'rental_hero_title'    => 'RENTAL',
            'rental_hero_subtitle' => 'RENTAL',
        ];

        foreach ($data as $key => $value) {
            if ($value === null) continue;

            if (is_array($value)) {
                $value = json_encode(array_values($value));
            }

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => $groupMap[$key] ?? 'GLOBAL',
                    'value' => $value,
                ]
            );
        }

        Setting::flushCache();

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}