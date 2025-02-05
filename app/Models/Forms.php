<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    use HasFactory;


    protected $fillable = [
        'fiche_id',
        'etape_id',
        'etape_number',
        'version_id',
        'form_title',
        'type'
    ];

    public $timestamps = false;

    public function etape()
    {
        return $this->belongsTo(Etape::class, 'etape_number');
    }

    public function formsConfig()
    {
        return $this->hasMany(FormConfig::class, 'form_id');
    }
}
