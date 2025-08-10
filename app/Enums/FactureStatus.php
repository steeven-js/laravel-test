<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum FactureStatus: string implements HasColor, HasIcon, HasLabel
{
    case Brouillon = 'brouillon';
    case Emise = 'emise';
    case Envoyee = 'envoyee';
    case Payee = 'payee';
    case EnRetard = 'en_retard';
    case Annulee = 'annulee';

    public function getLabel(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Emise => 'Emise',
            self::Envoyee => 'Envoyée',
            self::Payee => 'Payée',
            self::EnRetard => 'En retard',
            self::Annulee => 'Annulée',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Brouillon => 'gray',
            self::Emise => 'info',
            self::Envoyee => 'warning',
            self::Payee => 'success',
            self::EnRetard => 'danger',
            self::Annulee => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Brouillon => 'heroicon-m-document-text',
            self::Emise => 'heroicon-m-document',
            self::Envoyee => 'heroicon-m-paper-airplane',
            self::Payee => 'heroicon-m-banknotes',
            self::EnRetard => 'heroicon-m-exclamation-triangle',
            self::Annulee => 'heroicon-m-x-circle',
        };
    }
}
