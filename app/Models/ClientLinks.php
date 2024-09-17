<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientLinks extends Model
{
    use HasFactory;

    protected $table = 'clients_links';

    public function client_parent()
{
    return $this->belongsTo(Client::class, 'client_parent','id');
}

public function client_child()
{
    return $this->belongsTo(Client::class, 'client_id','id');
}
}
