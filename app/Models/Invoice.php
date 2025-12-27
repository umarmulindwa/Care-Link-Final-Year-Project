<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $connection = "bsc_connection";

    protected $appends = [
        'total_amount', 'provider', 'invoice_manager_name',
        'assigned_staff_name', 'duration', 'fund_commitment',
        'provider_name', 'provider_number', 'user_type', 'provider_email',
        // 'statuses'
    ];



    public function statuses()
    {
    }

    public function timeline()
    {
        return $this->hasMany(InvoiceTimeline::class);
    }

    public function getStatusesAttribute()
    {
        return $this->hasMany(InvoiceStatus::class)->orderBy('created_at', 'desc');
    }

    public function getTotalAmountAttribute()
    {
        return $this->invoice_value_amount + $this->tourism_levy_amount
            + $this->exise_duty_amount + $this->other_taxes_amount + $this->vat_amount;
    }
    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
    public function service_provider()
    {
        return $this->belongsTo('App\ServiceProvider');
    }

    public function ip_profile()
    {
        return $this->belongsTo(ipProfile::class);
    }

    // public function getProviderAttribute() {
    //     return $this->service_provider_id != NULL
    //                 ? ServiceProvider::find($this->service_provider_id)
    //                 : IPProfile::find($this->ip_profile_id);
    // }

    public function getProviderAttribute()
    {
        return $this->service_provider_id ? ServiceProvider::find($this->service_provider_id) : NULL;
    }

    public function getProviderNameAttribute()
    {
        if ($this->service_provider_id != null) {
            $sp = ServiceProvider::find($this->service_provider_id);
            return $sp != null ? $sp->name : 'Unknown name (' . $this->service_provider_id . ')';
        } else {
            $ip = IPProfile::find($this->ip_profile_id);
            $user = $ip != null ? User::find($ip->user_id)->name : '';
            return $user;
            // return User::find($ip->user_id)->name;
        }
    }

    // public function getProviderNameAttribute() {
    //     if($this->service_provider_id != null) {
    //         return ServiceProvider::find($this->service_provider_id)->name;
    //     } else {
    //         $ip = IPProfile::find($this->ip_profile_id);
    //         return User::find($ip->user_id)->name;
    //     }
    // }

    public function getProviderEmailAttribute()
    {
        if ($this->service_provider_id != null) {
            $sp = ServiceProvider::find($this->service_provider_id);
            return $sp != null ? $sp->email : '';
        } else {
            $ip = IPProfile::find($this->ip_profile_id);
            $email = $ip != null ? $ip->email : '';

            return $email;
        }
    }

    public function getProviderNumberAttribute()
    {
        if ($this->service_provider_id != null) {
            $sp = ServiceProvider::find($this->service_provider_id);
            return $sp != null ? $sp->contractor_number : 'Unknown Contract (' . $this->service_provider_id . ')';
        } else {
            return NULL;
        }
    }

    public function getUserTypeAttribute()
    {
        return $this->service_provider_id != null ? 'sp' : 'ip';
    }

    public function getInvoiceManagerNameAttribute()
    {
        $staff = StaffProfile::where('email', $this->invoice_manager)->first();

        if ($staff != null) {
            if ($staff->exited_unicef != 0) {
                $getSubstitute = getExitedStaffOic($staff);
                if ($getSubstitute != null) {
                    $staff = $getSubstitute;
                }
            }
        }

        return $this->invoice_manager != NULL && $staff != NULL ? $staff->name : NULL;
    }

    public function getAssignedStaffNameAttribute()
    {
        $staff = StaffProfile::where('email', $this->assigned_staff)->first();
        if ($staff != null) {
            if ($staff->exited_unicef != 0) {
                $getSubstitute = getExitedStaffOic($staff);
                if ($getSubstitute != null) {
                    $staff = $getSubstitute;
                }
            }
        }
        return $this->assigned_staff != NULL && $staff != NULL ? $staff->name : NULL;
    }

    public function getFundCommitmentAttribute()
    {
        return json_decode($this->fund_commitment_number);
    }

    public function getDurationAttribute()
    {
        $first = InvoiceTimeline::where('invoice_id', $this->id)->orderBy('created_at', 'ASC')->first();
        $timezone = timezone_open(env('TIMEZONE'));

        $now = time() + (3 * 60 * 60);
        $firstDate = $first != NULL ? strtotime($first->created_at) : strtotime($this->created_at);

        $reminder = ($now - $firstDate) / (60 * 60);
        $hours = $reminder % 24;
        $days = floor($reminder / 24);

        if ($days > 0) {
            return $hours > 0 ? $days . ' days ' . $hours . ' hours' : $days . ' days';
        } else {
            return $hours > 0 ? $hours . ' hours' : '1 hour';
        }
    }

    // commented out this to test the date
    public function getCreatedAtAttribute($value)
    {
        if ($value != null) {
            $date = $this->asDateTime($value);
            return $date->timezone(env('TIMEZONE'));
        }
        return NULL;
    }

    public function getUpdatedAtAttribute($value)
    {
        if ($value != null) {
            $date = $this->asDateTime($value);
            return $date->timezone(env('TIMEZONE'));
        }
        return NULL;
    }

    public function getStampedDateAttribute($value)
    {
        if ($value != null) {
            $date = $this->asDateTime($value);
            return $date->timezone(env('TIMEZONE'));
        }
        return NULL;
    }

    public function getClosedDateAttribute($value)
    {
        if ($value != null) {
            $date = $this->asDateTime($value);
            return $date->timezone(env('TIMEZONE'));
        }
        return NULL;
    }

}
