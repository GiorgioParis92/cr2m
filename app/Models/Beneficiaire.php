<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'prenom',
        'adresse',
        'cp',
        'ville',
        'telephone',
        'telephone_2',
        'email',
        'menage_mpr',
        'chauffage',
        'occupation',
        'lat',
        'lng'
    ];

    public function dossiers()
    {
        return $this->hasMany(Dossier::class, 'beneficiaire_id');
    }

}
