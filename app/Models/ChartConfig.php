<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartConfig extends Model
{
    protected $fillable = [
        'name',
        'description',
        'chart_type',
        'query_method',
        'params',
        'meta_key',
        'meta_value',
    ];

    protected $casts = [
        'params' => 'array',
    ];
}
