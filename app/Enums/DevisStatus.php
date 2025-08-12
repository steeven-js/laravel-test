<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DevisStatus: string implements HasColor, HasIcon, HasLabel
{
    case Brouillon = 'brouillon';
    case EnAttente = 'en_attente';
    case Accepte = 'accepte';
    case Refuse = 'refuse';
    case Expire = 'expire';

    public function getLabel(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::EnAttente => 'En attente',
            self::Accepte => 'Accepté',
            self::Refuse => 'Refusé',
            self::Expire => 'Expiré',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Brouillon => 'gray',
            self::EnAttente => 'warning',
            self::Accepte => 'success',
            self::Refuse => 'danger',
            self::Expire => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Brouillon => 'heroicon-m-document-text',
            self::EnAttente => 'heroicon-m-clock',
            self::Accepte => 'heroicon-m-check-circle',
            self::Refuse => 'heroicon-m-x-circle',
            self::Expire => 'heroicon-m-exclamation-triangle',
        };
    }
}
