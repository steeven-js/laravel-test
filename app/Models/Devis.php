<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use \App\Models\Traits\HasHistorique, HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_devis',
        'client_id',
        'administrateur_id',
        'date_devis',
        'date_validite',
        'statut',
        'statut_envoi',
        'date_envoi_client',
        'date_envoi_admin',
        'pdf_file',
        'pdf_url',
        'objet',
        'description',
        'montant_ht',
        'taux_tva',
        'montant_tva',
        'montant_ttc',
        'conditions',
        'notes',
        'date_acceptation',
        'archive',
    ];

    protected $casts = [
        'date_devis' => 'date',
        'date_validite' => 'date',
        'date_envoi_client' => 'datetime',
        'date_envoi_admin' => 'datetime',
        'date_acceptation' => 'date',
        'archive' => 'boolean',
        'montant_ht' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function administrateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrateur_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneDevis::class, 'devis_id')->orderBy('ordre');
    }

    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'devis_id');
    }
}
