<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormConfig extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'forms_config';

    // If your table doesn't have timestamp columns, disable them
    public $timestamps = false;

    // Allow mass assignment on these attributes
    protected $fillable = [
        'form_id',
        'ordering',
        // Add other columns you want to be mass assignable
    ];

    // Optionally, cast attributes to appropriate data types
    protected $casts = [
        'form_id' => 'integer',
        'ordering' => 'integer',
        // Add other casts as needed
    ];
}
