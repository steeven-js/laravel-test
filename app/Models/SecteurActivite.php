<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecteurActivite extends Model
{
    use \App\Models\Traits\HasHistorique, HasFactory, SoftDeletes;

    protected $table = 'secteurs_activite';

    protected $fillable = [
        'code',
        'libelle',
        'division',
        'section',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function entreprises(): HasMany
    {
        return $this->hasMany(Entreprise::class, 'secteur_activite_id');
    }
}
