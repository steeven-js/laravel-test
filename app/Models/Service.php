<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

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


