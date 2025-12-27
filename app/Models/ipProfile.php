@ -1,40 +0,0 @@
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class IPProfile extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        'password',
        'logo',
        'token',
        'auth_code',
        'is_authenticated'
    ];

    protected $appends = ['full_role'];

    protected $hidden = ['password', 'auth_code'];

    protected $with = ['user'];

    public function getFullRoleAttribute()
    {
        return $this->role == 'admin' ? 'Administrator' : 'Staff';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
