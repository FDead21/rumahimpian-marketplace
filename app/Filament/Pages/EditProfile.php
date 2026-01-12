<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title = 'Edit My Profile';
    protected static string $view = 'filament.pages.edit-profile';
    // Push it to the bottom of the menu
    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        // Load current user data into the form
        $this->form->fill(auth()->user()->attributesToArray());
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Update your account details and public profile.')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url') 
                            ->label('Profile Photo')
                            ->avatar()
                            ->image()
                            ->directory('avatars'),

                        Forms\Components\TextInput::make('name')
                            ->required(),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true), // Ignore current user's email for unique check

                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required(),
                            
                        Forms\Components\Textarea::make('bio')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password to stay secure.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->requiredWith('new_password')
                            ->currentPassword(), // Custom validation rule

                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->confirmed() // Checks for new_password_confirmation field
                            ->rule(Password::default()),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password(),
                    ])->collapsed(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Handle Password Update independently
        if (!empty($data['new_password'])) {
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);
        }

        // Update other fields (excluding password fields)
        $userData = collect($data)->except(['current_password', 'new_password', 'new_password_confirmation'])->toArray();
        $user->update($userData);

        Notification::make()
            ->success()
            ->title('Profile updated successfully')
            ->send();
    }
}