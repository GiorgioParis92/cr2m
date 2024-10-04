<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etape extends Model
{
    use HasFactory;

    protected $fillable = [
        'etape_name',
        'etape_desc',
        'etape_style',
        'etape_icon',
        'order_column',
    ];

    public function dossiers()
    {
        return $this->hasMany(Dossier::class, 'etape_id');
    }

    public function statuses()
    {
        return $this->hasMany(Status::class, 'etape_id');
    }
    public function forms()
    {
        return $this->hasMany(Form::class, 'etape_number');
    }
}
