<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use \App\Models\Traits\HasHistorique, HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'description',
        'prix_ht',
        'qte_defaut',
        'unite',
        'actif',
    ];
}
