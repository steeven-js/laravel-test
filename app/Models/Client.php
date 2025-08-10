<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'actif',
        'notes',
        'entreprise_id',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function devis(): HasMany
    {
        return $this->hasMany(Devis::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(ClientEmail::class);
    }

    public function historiques(): MorphMany
    {
        return $this->morphMany(Historique::class, 'entite', 'entite_type', 'entite_id');
    }

    // Méthodes pour récupérer les devis du client
    public function getDevisAcceptes()
    {
        return $this->devis()->where('statut', 'accepte')->get();
    }

    public function getDevisEnAttente()
    {
        return $this->devis()->where('statut', 'en_attente')->get();
    }

    public function getDevisRefuses()
    {
        return $this->devis()->where('statut', 'refuse')->get();
    }

    public function getDevisParStatut($statut)
    {
        return $this->devis()->where('statut', $statut)->get();
    }

    public function getDevisRecents($limit = 5)
    {
        return $this->devis()->latest()->limit($limit)->get();
    }

    public function getTotalDevis()
    {
        return $this->devis()->count();
    }

    public function getTotalDevisAcceptes()
    {
        return $this->devis()->where('statut', 'accepte')->count();
    }

    public function getTauxConversionDevis()
    {
        $total = $this->getTotalDevis();
        if ($total === 0) {
            return 0;
        }
        
        return round(($this->getTotalDevisAcceptes() / $total) * 100, 2);
    }

    public function getDevisParPeriode($debut, $fin)
    {
        return $this->devis()
            ->whereBetween('created_at', [$debut, $fin])
            ->get();
    }
}
