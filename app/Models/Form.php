<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
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
}
