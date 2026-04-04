<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rerral extends Model
{
    use HasFactory;
      protected $table = 'referrals';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'patient_id',
        'from_doctor_id',
        'to_hospital_id',
        'reason',
        'status',
        'referral_date',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'referral_date' => 'date',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Referral belongs to a patient
    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    // Referral made by a doctor
    public function fromDoctor()
    {
        return $this->belongsTo(User::class, 'from_doctor_id');
    }

    // Referral sent to a hospital
    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'to_hospital_id');
    }

   

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
