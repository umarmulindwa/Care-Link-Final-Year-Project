<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StaffIdentity extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_profiles_id',
        'un_id',
        'un_id_expiry',
        'diplomatic_passport',
        'diplomatic_passport_expiry',
        'national_passport',
        'national_passport_expiry',
        'visa',
        'visa_expiry',
        'visa_type',
    ];

    protected array $dates = [
        'un_id_expiry',
        'diplomatic_passport_expiry',
        'national_passport_expiry',
        'visa_expiry',
    ];

    // Accessor for un_id_expiry
    public function getUnIdExpiryAttribute($value): ?string
    {
        return $this->formatDate($value);
    }

    // Accessor for diplomatic_passport_expiry
    public function getDiplomaticPassportExpiryAttribute($value): ?string
    {
        return $this->formatDate($value);
    }

    // Accessor for national_passport_expiry
    public function getNationalPassportExpiryAttribute($value): ?string
    {
        return $this->formatDate($value);
    }

    // Accessor for visa_expiry
    public function getVisaExpiryAttribute($value): ?string
    {
        return $this->formatDate($value);
    }

    // Helper method to format date
    protected function formatDate($value): ?string
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function staff_files(): MorphMany
    {
        return $this->morphMany(StaffFile::class,'storeable');
    }

}
