<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'termine',
        'ordre',
        'priorite',
        'date_echeance',
        'client_id',
        'user_id',
    ];

    protected $casts = [
        'termine' => 'boolean',
        'ordre' => 'integer',
        'date_echeance' => 'date',
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


