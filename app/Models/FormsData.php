<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormsData extends Model
{
    use HasFactory;

    protected $table = 'forms_data';
    protected $fillable = ['dossier_id', 'form_id', 'meta_key', 'meta_value'];

}
