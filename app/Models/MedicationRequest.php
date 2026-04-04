<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationRequest extends Model
{
    use HasFactory;
protected $table = 'medication_requests';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'medication_id',
        'request_type',
        'status',
        'requested_at',
        'approved_by',
        'delivery_address',
    ];

    /**
     * Casts (important for dates & enums)
     */
    protected $casts = [
        'requested_at' => 'datetime',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // A request belongs to a patient
    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    // A request belongs to a medication
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    // Approved by a doctor/admin (user)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // One request has one delivery
    public function delivery()
    {
        return $this->hasOne(MedicationDelivery::class, 'request_id');
    }

    /**
     * =========================
     * SCOPES (VERY USEFUL 🔥)
     * =========================
     */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
