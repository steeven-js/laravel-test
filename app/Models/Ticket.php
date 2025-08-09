<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titre',
        'description',
        'priorite',
        'statut',
        'type',
        'client_id',
        'user_id',
        'created_by',
        'notes_internes',
        'solution',
        'date_resolution',
        'date_echeance',
        'temps_estime',
        'temps_passe',
        'progression',
        'visible_client',
    ];

    protected $casts = [
        'date_resolution' => 'datetime',
        'date_echeance' => 'datetime',
        'temps_estime' => 'integer',
        'temps_passe' => 'integer',
        'progression' => 'integer',
        'visible_client' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
