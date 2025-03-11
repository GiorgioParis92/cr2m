<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dossiers extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiaire_id',
        'client_id',
        'folder',
        'fiche_id',
        'etape_id',
        'status_id',
        'mar',
        'mandataire_financier',
        'installateur',
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
        return $this->belongsTo(Etape::class, 'etape_number','id');
    }
    public function get_rdv()
    {
        return $this->hasMany(Rdv::class, 'dossier_id','id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function mandataire_financier()
    {


        return $this->belongsTo(Client::class,'mandataire_financier', 'id');
    }

    public function mar()
    {
        return $this->belongsTo(Client::class,'mar', 'id');
    }
    public function mar_client()
    {
        return $this->belongsTo(Client::class,'mar', 'id');
    }
    public function installateur()
    {
        return $this->belongsTo(Client::class,'installateur', 'id');
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

    public function dossiersData()
{
    return $this->hasMany(DossiersData::class, 'dossier_id');
}
public function formsData()
{
    return $this->hasMany(FormsData::class, 'dossier_id');
}
public function dossiersDataByMetaKey($metaKey)
{
    return $this->hasMany(DossiersData::class, 'dossier_id')
                ->where('meta_key', $metaKey);
}
}
