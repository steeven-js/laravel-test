<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'nom_commercial',
        'siret',
        'siren',
        'secteur_activite',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'telephone',
        'email',
        'site_web',
        'actif',
        'notes',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function secteurActivite(): BelongsTo
    {
        return $this->belongsTo(SecteurActivite::class, 'secteur_activite_id');
    }
}
