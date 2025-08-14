<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.pages.profile-settings';

    protected static ?string $slug = 'settings/profile';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Paramètres du profil';

    protected static ?string $title = 'Paramètres du profil';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?int $navigationSort = 1;

    public ?array $profileData = [];

    public ?array $passwordData = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User; // Tout utilisateur authentifié peut accéder à ses paramètres
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->profileForm->fill($this->getUserProfileData());
    }

    protected function getUserProfileData(): array
    {
        /** @var User $user */
        $user = Auth::user();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'ville' => $user->ville,
            'adresse' => $user->adresse,
            'code_postal' => $user->code_postal,
            'pays' => $user->pays,
            'avatar' => $user->avatar,
        ];
    }

    public function getForms(): array
    {
        return [
            'profileForm' => $this->makeForm()
                ->schema($this->getProfileSchema())
                ->statePath('profileData'),
            'passwordForm' => $this->makeForm()
                ->schema($this->getPasswordSchema())
                ->statePath('passwordData'),
        ];
    }

    protected function getProfileSchema(): array
    {
        return [
            Forms\Components\Section::make('Profil')
                ->description('Ajoutez une photo à votre profil et mettez à jour vos informations personnelles')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\FileUpload::make('avatar')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('avatars')
                                ->visibility('public')
                                ->avatar()
                                ->imageCropAspectRatio('1:1')
                                ->columnSpan(1),
                            Forms\Components\Grid::make(2)
                                ->columnSpan(2)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nom complet')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->label('Adresse email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('telephone')
                                        ->label('Téléphone')
                                        ->tel()
                                        ->maxLength(50),
                                    Forms\Components\TextInput::make('ville')
                                        ->label('Ville')
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('adresse')
                                        ->label('Adresse complète')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('code_postal')
                                        ->label('Code postal')
                                        ->maxLength(20),
                                    Forms\Components\TextInput::make('pays')
                                        ->label('Pays')
                                        ->maxLength(100),
                                ]),
                        ]),
                ])
                ->columns(1),
        ];
    }

    protected function getPasswordSchema(): array
    {
        return [
            Forms\Components\Section::make('Mot de passe')
                ->description('Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé')
                ->schema([
                    Forms\Components\TextInput::make('current_password')
                        ->label('Mot de passe actuel')
                        ->password()
                        ->required(),
                    Forms\Components\TextInput::make('new_password')
                        ->label('Nouveau mot de passe')
                        ->password()
                        ->rule(PasswordRule::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised())
                        ->required(),
                    Forms\Components\TextInput::make('new_password_confirmation')
                        ->label('Confirmer le mot de passe')
                        ->password()
                        ->same('new_password')
                        ->required(),
                ])
                ->columns(1),
        ];
    }

    public function saveProfile(): void
    {
        $this->profileData = $this->profileForm->getState();

        /** @var User $user */
        $user = Auth::user();

        $user->fill([
            'name' => $this->profileData['name'] ?? $user->name,
            'email' => $this->profileData['email'] ?? $user->email,
            'telephone' => $this->profileData['telephone'] ?? null,
            'ville' => $this->profileData['ville'] ?? null,
            'adresse' => $this->profileData['adresse'] ?? null,
            'code_postal' => $this->profileData['code_postal'] ?? null,
            'pays' => $this->profileData['pays'] ?? null,
            'avatar' => $this->profileData['avatar'] ?? $user->avatar,
        ]);

        $user->save();

        Notification::make()
            ->title('Profil mis à jour')
            ->success()
            ->send();
    }

    public function savePassword(): void
    {
        $this->passwordData = $this->passwordForm->getState();

        /** @var User $user */
        $user = Auth::user();

        $current = (string) ($this->passwordData['current_password'] ?? '');
        $new = (string) ($this->passwordData['new_password'] ?? '');
        $confirm = (string) ($this->passwordData['new_password_confirmation'] ?? '');

        if (! Hash::check($current, (string) $user->getAuthPassword())) {
            Notification::make()
                ->title('Le mot de passe actuel est incorrect')
                ->danger()
                ->send();

            return;
        }

        if ($new !== $confirm) {
            Notification::make()
                ->title('La confirmation ne correspond pas')
                ->danger()
                ->send();

            return;
        }

        $rules = ['new_password' => [PasswordRule::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()]];
        $this->validate($rules, [], ['new_password' => 'nouveau mot de passe']);

        $user->password = $new; // cast 'hashed' s'appliquera
        $user->save();

        $this->passwordForm->fill([]);

        Notification::make()
            ->title('Mot de passe mis à jour')
            ->success()
            ->send();
    }
}
