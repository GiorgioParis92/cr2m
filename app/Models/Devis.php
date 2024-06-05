<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $table = 'devis';
    protected $primaryKey = 'devis_id';

    protected $fillable = ['devis_name', 'client_id', 'amount'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'devis_id');
    }
}
