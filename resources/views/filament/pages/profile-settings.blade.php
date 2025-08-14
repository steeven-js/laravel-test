@php /** @var \App\Filament\Pages\ProfileSettings $this */ @endphp

<x-filament::page>
	<div class="space-y-8">
		<x-filament::section>
			<x-slot name="heading">Profil</x-slot>
			<x-slot name="description">Ajoutez une photo à votre profil et mettez à jour vos informations personnelles</x-slot>

			{{ $this->profileForm }}

			<x-slot name="footer">
				<x-filament::button wire:click="saveProfile" color="primary">
					Sauvegarder le profil
				</x-filament::button>
			</x-slot>
		</x-filament::section>

		<x-filament::section>
			<x-slot name="heading">Mot de passe</x-slot>
			<x-slot name="description">Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé</x-slot>

			{{ $this->passwordForm }}

			<x-slot name="footer">
				<x-filament::button wire:click="savePassword" color="warning">
					Sauvegarder le mot de passe
				</x-filament::button>
			</x-slot>
		</x-filament::section>
	</div>
</x-filament::page>


