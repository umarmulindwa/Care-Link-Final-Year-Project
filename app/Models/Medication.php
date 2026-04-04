<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;
        protected $table = 'medications';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'dosage',
        'manufacturer',
        'stock_quantity',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // A medication can have many requests
    public function medicationRequests()
    {
        return $this->hasMany(MedicationRequest::class);
    }

   

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}
