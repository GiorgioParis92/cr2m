<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientLinks extends Model
{
    use HasFactory;

    protected $table = 'clients_links';
    protected $fillable = [
        'client_id',      // Add client_id to the fillable array
        'client_parent',  // Add client_parent or other fields you use in mass assignment
        // Add any other fields that should be mass assignable
    ];
    public function client_parent()
{
    return $this->belongsTo(Client::class, 'client_parent','id');
}

public function client_child()
{
    return $this->belongsTo(Client::class, 'client_id','id');
}
}
