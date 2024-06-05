<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    

    public $timestamps = true;
    protected $fillable = [
        'client_title',
        'type_client',
        'main_logo',
        'adresse',
        'cp',
        'ville',
        'email',
        'telephone',
        'siret',
        'siren',
        'tva_intracomm',
        'type_societe',
        'rcs',
        'naf',
        'agrement',
    ];

    public function type()
    {
        return $this->belongsTo(ClientType::class, 'type_client');
    }

}
