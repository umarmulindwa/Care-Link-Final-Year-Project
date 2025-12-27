<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ServiceProvider extends Model
{
    use Notifiable;

    protected $connection = 'mysql';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'contractor_number',
        'vendor_number',
        'password',
        'logo',
        'token',
        'auth_code',
        'is_authenticated',
        'street'
    ];


    protected $hidden = ['password', 'auth_code'];

    protected $appends = ['permissions'];

    public function getPermissionsAttribute()
    {
        return collect(UserPermission::whereHas('user', function ($query) {
            $query->where('email', $this->email);
        })->get())->map(function ($d) {
            return $d->permission;
        })->values()->all();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'email','email');
    }

    public function vehicles()
    {
        return $this->hasMany(SPVehicle::class, 'user_id', 'service_provider_id');
    }

    public function drivers()
    {
        return $this->hasMany(SPDriver::class, 'user_id', 'service_provider_id');
    }
}
