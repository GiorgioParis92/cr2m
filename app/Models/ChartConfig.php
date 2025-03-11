<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartConfig extends Model
{
    protected $table = 'chart_config';

    protected $casts = [
        'parameters' => 'array',
    ];
}
