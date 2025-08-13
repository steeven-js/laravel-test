<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use \App\Models\Traits\HasHistorique, HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'etape',
        'probabilite',
        'montant',
        'date_cloture_prevue',
        'date_cloture_reelle',
        'client_id',
        'user_id',
        'notes',
        'active',
    ];

    protected $casts = [
        'probabilite' => 'integer',
        'montant' => 'decimal:2',
        'date_cloture_prevue' => 'date',
        'date_cloture_reelle' => 'date',
        'active' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
