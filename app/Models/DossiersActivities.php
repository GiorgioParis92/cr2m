<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossiersActivities extends Model
{
    use HasFactory;

    protected $fillable = ['dossier_id', 'user_id', 'form_id', 'activity'];

    // Assuming that there are related models for Dossier, User, and Form
    public function dossier()
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    // Custom function to get information based on dossier_id, user_id, and form_id
    public static function getDossierInfo($dossierId, $userId, $formId)
    {
        return self::where('dossier_id', $dossierId)
                    ->where('user_id', $userId)
                    ->where('form_id', $formId)
                    ->with(['dossier', 'user', 'form']) // eager load related models
                    ->first();
    }
}
