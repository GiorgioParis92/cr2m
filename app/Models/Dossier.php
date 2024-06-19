<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiaire_id',
        'client_id',
        'fiche_id',
        'etape_id',
        'status_id',
        'mar',
        'mandataire_financier',
        'lat',
        'lng'
    ];

    public function beneficiaire()
    {
        return $this->belongsTo(Beneficiaire::class, 'beneficiaire_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function fiche()
    {
        return $this->belongsTo(Fiche::class, 'fiche_id');
    }

    public function etape()
    {
        return $this->belongsTo(Etape::class, 'etape_number');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function getMarClientAttribute()
    {
        return Client::find($this->mar);
    }

    public function getMandataireFinancierClientAttribute()
    {
        return Client::find($this->mandataire_financier);
    }
    public function getInstallateurClientAttribute()
    {
        return Client::find($this->installateur);
    }
}
