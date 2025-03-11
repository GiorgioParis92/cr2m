<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id','dossier_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'card_user'); // Assuming the pivot table is named card_user
}

public function dossier()
{
    return $this->belongsTo(Dossier::class, 'dossier_id');
}
public function archivedByUser()
{
    return $this->belongsTo(User::class, 'archived_by');
}
}
