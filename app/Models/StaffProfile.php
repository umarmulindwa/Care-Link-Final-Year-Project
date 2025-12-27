<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class StaffProfile extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        "personal_id", "index_number", "name", "email", "staff_type", "position_text", "position_id", "section", "organisation_unit", "grade", "reports_to", "category","categories", "gender",
        "appointment_type", "date_began_working_with_unicef", "date_began_working_with_unicef_country_level", "date_contract_end", "is_imported", "marital_status",
        "phone", "phone_whatsapp", "allocated_phone_number","address", "lat", "lng", "pillar", "extension_id",
        "orientation_status", "orientation_stage", "role", 'exited_unicef', 'isOnLeave', 'leaveOIC','blood_group','p_number','radio_type','call_sign'
    ];

    protected $appends = ['permissions', 'roles', 'is_on_leave', 'is_section_chief', 'sectionname', 'groupDetails', 'dependants','unicef_phone_number'];

    public function pillar()
    {
        return $this->belongsTo(Pillar::class, 'pillar');
    }

    // public function section()
    // {
    //     return $this->belongsTo(Section::class, 'section');
    // }


    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
    // public function organisationUnit()
    // {
    //     return $this->belongsTo(OrganisationUnit::class, 'id', 'organisation_unit');
    // }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type');
    }

    public function getSectionnameAttribute()
    {
        $profile = Section::where('id', $this->section)->first();
        return is_null($profile) ? null : $profile->name;
    }

    public function getgroupDetailsAttribute()
    {
        $groupDetails = GroupStaff::where('staff_profile_id', $this->id)->with(['group'])->first();
        return $groupDetails;
    }



    public function getIsOnLeaveAttribute()
    {

        return Leave::where('leaving_staff', $this->email)
            // ->where('status','approved')
            ->whereDate('start', '<=', date("Y-m-d"))
            ->WhereDate('end', '>=', date("Y-m-d"))
            ->count() > 0;

        // return Leave::where('personal_id', $this->personal_id)
        //                 ->whereDate('end', '>=', date("Y-m-d"))
        //                 ->count() > 0;
    }

    // public function getIsDeputizingAttribute()
    // {
    //     return Leave::where('personal_id', $this->personal_id)
    //         ->where('status','deputizing')
    //         ->whereDate('start', '<=', date("Y-m-d"))
    //         ->WhereDate('end', '>=', date("Y-m-d"))
    //         ->count() > 0;

    // }

    // public function getPermissionsAttribute()
    // {

    //     return collect(UserPermission::whereHas('user', function ($query) {
    //         $query->where('email', $this->email);
    //     })->get())->map(function ($d) {
    //         return $d->permission;
    //     })->values()->all();
    // }
    public function getPermissionsAttribute()
    {
        $user = User::where('email', $this->email)->first();
        if ($user == null) {
            return [];
        }

        return $user->all_permissions;
    }

    public function getRolesAttribute(){
        $user = User::where('email', $this->email)->first();
        if ($user == null) {
            return [];
        }
        $userRoles = $user->roles;
        return $userRoles;
    }

    public function getdependantsAttribute()
    {
        $userDetails = User::where('email', $this->email)->first();
        if ($userDetails != null) {
            $dependants = DependantDetail::where('staff_id', $userDetails->id)->latest()->get();
            return  $dependants;
        } else {
            return null;
        }
    }

    public function getIsSectionChiefAttribute()
    {
        return Section::where('staff_chief_id', $this->id)->first() != null;
    }

    public function organisationUnit()
    {
        return $this->belongsTo(OrganisationUnit::class);
    }

    public function staffIdentity(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StaffIdentity::class)->with('staff_files');
    }



}
