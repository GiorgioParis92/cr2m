<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = [
        'etape_id',
        'status_name',
        'status_desc',
        'status_style',
        'status_icon',
    ];

    public function etape()
    {
        return $this->belongsTo(Etape::class, 'etape_id');
    }

    public function dossiers()
    {
        return $this->hasMany(Dossier::class, 'status_id');
    }
}
