<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
