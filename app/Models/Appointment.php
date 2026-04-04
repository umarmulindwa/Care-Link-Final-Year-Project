<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

        protected $table = 'appointments';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'appointment_date',
        'status',
        'reason',
        'clinic_location',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Appointment belongs to a patient
    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    // Appointment belongs to a doctor
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    // Appointment has notes
    public function notes()
    {
        return $this->hasMany(AppointmentNote::class);
    }

   

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

}
