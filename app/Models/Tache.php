<?php 


// app/Models/Tache.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tache extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'numero_dossier',
        'objet',
        'date_reception',
        'date_affectation',
        'type_demande',
        'agent_id',
        'commentaires',
        'valide',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
