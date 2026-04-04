<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationDelivery extends Model
{
    use HasFactory;

    protected $table = 'medication_deliveries';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'request_id',
        'delivery_status',
        'delivery_date',
        'delivered_by',
        'tracking_number',
        'notes',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Delivery belongs to a medication request
    public function medicationRequest()
    {
        return $this->belongsTo(MedicationRequest::class, 'request_id');
    }

    public function scopePending($query)
    {
        return $query->where('delivery_status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('delivery_status', 'delivered');
    }
}
