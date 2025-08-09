<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Madinia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'madinia';

    protected $fillable = [
        'name',
        'contact_principal_id',
        'telephone',
        'email',
        'site_web',
        'siret',
        'numero_nda',
        'pays',
        'adresse',
        'description',
        'reseaux_sociaux',
        'nom_compte_bancaire',
        'nom_banque',
        'numero_compte',
        'iban_bic_swift',
    ];

    protected $casts = [
        'reseaux_sociaux' => 'array',
    ];

    public function contactPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_principal_id');
    }
}
