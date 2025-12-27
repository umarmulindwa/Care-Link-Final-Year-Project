<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_type', 'active_directory', 'token', 'current_team_id','password_expires_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_expires_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */

    protected $appends = [
        'profile_photo_url',
        'directPermissions',
        'permissionsViaRoles',
        'allPermissions',
        'roles',
        'notifications',
        "staff_profile",
        "password_expired",
    ];

    // public function getStaffProfileAttribute()
    // {
    //     return $this->hasOne(StaffProfile::class, 'email', 'email');
    // }

    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class, 'email', 'email');
    }

    public function getdirectPermissionsAttribute()
    {
        $directPermissions = $this->getDirectPermissions()->pluck('name')->all();
        return $directPermissions;
    }
    public function getpermissionsViaRolesAttribute()
    {
        $permissionsViaRoles = $this->getPermissionsViaRoles()->pluck('name')->all();
        return $permissionsViaRoles;
    }
    public function getallPermissionsAttribute()
    {
        $allPermissions = $this->getAllPermissions()->pluck('name')->all();
        return $allPermissions;
    }
    public function getStaffProfileAttribute(){
        return StaffProfile::where('email', $this->email)->with('staffIdentity')->first();
    }
    public function getrolesAttribute()
    {
        $roles = $this->roles()->get();
        return $roles;
    }
    public function getNotificationsAttribute()
    {
        $notificaitons = Notification::where('personal_id', $this->email)->latest()->get();
        return $notificaitons;
    }

    public function staffProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StaffProfile::class, 'email', 'email');
    }

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordChangeHistory::class);
    }

    public function getPasswordExpiredAttribute(): bool
    {
        return Carbon::now()->diffInDays($this->password_expires_at, false) <= 0;
    }
}
