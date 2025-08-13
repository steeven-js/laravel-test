<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use \App\Models\Traits\HasHistorique, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'subject',
        'body',
        'is_default',
        'is_active',
        'variables',
        'description',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (EmailTemplate $template): void {
            if ($template->is_default && $template->isDirty('is_default')) {
                // Désactive tous les autres modèles par défaut dans la même catégorie AVANT la sauvegarde
                static::query()
                    ->where('category', $template->category)
                    ->whereKeyNot($template->getKey())
                    ->update(['is_default' => false]);
            }
        });
    }
}
