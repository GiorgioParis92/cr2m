<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossiersData extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'dossiers_data';

    // Allow mass assignment on these attributes
    protected $fillable = ['dossier_id', 'meta_key', 'meta_value','user_id'];

    // Optionally, cast attributes to appropriate data types
    protected $casts = [
        'dossier_id' => 'integer',
    ];
}
