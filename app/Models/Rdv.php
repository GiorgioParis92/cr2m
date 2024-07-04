<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rdv extends Model
{
    use HasFactory;

    protected $table = 'rdv';

    public function status()
    {
        return $this->hasOne(RdvStatus::class, 'id','status');
    }


}





