<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;
     protected $table = 'hospitals';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'location',
        'address',
        'phone',
        'email',
        'type',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Hospital has many doctors
    public function doctors()
    {
        return $this->hasMany(User::class);
    }

    // Hospital receives referrals
    public function referrals()
    {
        return $this->hasMany(Rerral::class, 'to_hospital_id');
    }
}
