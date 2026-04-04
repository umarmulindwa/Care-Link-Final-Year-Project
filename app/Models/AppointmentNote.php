<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentNote extends Model
{
    use HasFactory;
    protected $table = 'appointment_notes';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'notes',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Note belongs to an appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Note belongs to a doctor
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }
}
