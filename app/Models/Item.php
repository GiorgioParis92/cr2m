<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['devis_id', 'product_id', 'quantity', 'price', 'discount','observations','product_name'];

    public function devis()
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
