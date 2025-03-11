<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultPermission extends Model
{
    use HasFactory;

    protected $table = 'default_permission';

    protected $fillable = [
        'permission_name',
        'type_id',
        'type_client',
        'is_active',
    ];

    // Relationship with User
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'type_id','id');
    }

    // Relationship with Client
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'type_client','id');
    }
}
