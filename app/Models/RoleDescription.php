<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleDescription extends Model
{
    use HasFactory;

    protected $fillable = [
         'role_id',
        'description',
        'created_by',
    ];

    public function role(){
        return $this->hasOne( Role::class, 'id', 'role_id');
    }
}
