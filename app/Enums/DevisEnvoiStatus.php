<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DevisEnvoiStatus: string implements HasColor, HasIcon, HasLabel
{
    case NonEnvoye = 'non_envoye';
    case Envoye = 'envoye';
    case EchecEnvoi = 'echec_envoi';

    public function getLabel(): string
    {
        return match ($this) {
            self::NonEnvoye => 'Non envoyé',
            self::Envoye => 'Envoyé',
            self::EchecEnvoi => 'Échec d\'envoi',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NonEnvoye => 'gray',
            self::Envoye => 'success',
            self::EchecEnvoi => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NonEnvoye => 'heroicon-m-paper-airplane',
            self::Envoye => 'heroicon-m-check-circle',
            self::EchecEnvoi => 'heroicon-m-exclamation-triangle',
        };
    }
}
