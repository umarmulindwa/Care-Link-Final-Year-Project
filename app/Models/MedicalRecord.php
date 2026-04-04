<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;
    protected $table = 'medical_records';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'appointment_id',
        'record_type',
        'description',
        'lab_results',
        'medications_prescribed',
        'record_date',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'record_date' => 'datetime',
        'lab_results' => 'array', // Store lab results as JSON
        'medications_prescribed' => 'array', // Store prescribed meds as JSON
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Medical record belongs to a patient
    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    // Medical record belongs to a doctor
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    // Medical record belongs to an appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
