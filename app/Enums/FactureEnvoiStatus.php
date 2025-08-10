<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum FactureEnvoiStatus: string implements HasColor, HasIcon, HasLabel
{
    case NonEnvoyee = 'non_envoyee';
    case Envoyee = 'envoyee';
    case EchecEnvoi = 'echec_envoi';

    public function getLabel(): string
    {
        return match ($this) {
            self::NonEnvoyee => 'Non envoyée',
            self::Envoyee => 'Envoyée',
            self::EchecEnvoi => 'Échec d\'envoi',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NonEnvoyee => 'gray',
            self::Envoyee => 'success',
            self::EchecEnvoi => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NonEnvoyee => 'heroicon-m-paper-airplane',
            self::Envoyee => 'heroicon-m-check-circle',
            self::EchecEnvoi => 'heroicon-m-exclamation-triangle',
        };
    }
}
