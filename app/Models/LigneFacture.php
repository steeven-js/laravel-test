<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LigneFacture extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lignes_factures';

    protected $fillable = [
        'facture_id',
        'service_id',
        'quantite',
        'unite',
        'prix_unitaire_ht',
        'remise_pourcentage',
        'taux_tva',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'ordre',
        'description_personnalisee',
    ];

    protected $casts = [
        'prix_unitaire_ht' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (LigneFacture $ligne): void {
            $quantite = max(1, (int) ($ligne->quantite ?? 1));
            $prixUnitaire = (float) ($ligne->prix_unitaire_ht ?? 0);
            $tauxTva = (float) ($ligne->taux_tva ?? 0);
            $remise = (float) ($ligne->remise_pourcentage ?? 0);
            $remise = max(0.0, min(100.0, $remise));

            $montantHtBrut = $quantite * $prixUnitaire;
            $montantHt = $montantHtBrut * (1 - ($remise / 100.0));
            $montantTva = $montantHt * ($tauxTva / 100.0);
            $montantTtc = $montantHt + $montantTva;

            $ligne->quantite = $quantite;
            $ligne->montant_ht = round($montantHt, 2);
            $ligne->montant_tva = round($montantTva, 2);
            $ligne->montant_ttc = round($montantTtc, 2);
            $ligne->remise_pourcentage = $remise;
        });
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
