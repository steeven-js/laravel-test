<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Historique extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false; // created_at géré manuellement par la migration

    protected $table = 'historique';

    protected $fillable = [
        'entite_type',
        'entite_id',
        'action',
        'titre',
        'description',
        'donnees_avant',
        'donnees_apres',
        'donnees_supplementaires',
        'user_id',
        'user_nom',
        'user_email',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
        'donnees_supplementaires' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entite(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'entite_type', 'entite_id');
    }
}
