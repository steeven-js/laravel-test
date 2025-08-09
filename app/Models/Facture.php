<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_facture',
        'devis_id',
        'client_id',
        'administrateur_id',
        'date_facture',
        'date_echeance',
        'statut',
        'statut_envoi',
        'pdf_file',
        'pdf_url',
        'objet',
        'description',
        'montant_ht',
        'taux_tva',
        'montant_tva',
        'montant_ttc',
        'conditions_paiement',
        'notes',
        'date_paiement',
        'mode_paiement',
        'reference_paiement',
        'archive',
        'date_envoi_client',
        'date_envoi_admin',
        'mode_paiement_propose',
        'stripe_payment_url',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_invoice_id',
        'stripe_customer_id',
        'stripe_receipt_url',
        'stripe_status',
        'stripe_metadata',
        'stripe_created_at',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
        'date_envoi_client' => 'datetime',
        'date_envoi_admin' => 'datetime',
        'archive' => 'boolean',
        'montant_ht' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'stripe_metadata' => 'array',
    ];

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class);
    }

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
        return $this->hasMany(LigneFacture::class, 'facture_id')->orderBy('ordre');
    }
}
