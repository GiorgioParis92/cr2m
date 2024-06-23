<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content','dossier_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

}
