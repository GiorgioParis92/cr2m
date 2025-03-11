<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    use HasFactory;

    protected $table = 'docs';
    protected $primaryKey = 'doc_id';
    protected $fillable = [
        'doc_title',
        'created_at',
        'updated_at',
        'doc_type',
        'doc_status',
        'doc_name'
    ];

    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id', 'campagne_id');
    }
}
