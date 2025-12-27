<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class GroupStaff extends Model
{
    use HasFactory;

    protected $connection = "communication_tree_connection";

    protected $appends = ['otherGroupMembers', 'groupLeader'];


    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }

    public function getotherGroupMembersAttribute()
    {
        $otherGroupStaff = GroupStaff::where('group_id', $this->group_id)
            ->where('id', '!=', $this->id)
            ->get();

        $staffProfileIds = $otherGroupStaff->pluck('staff_profile_id')->toArray();

        $otherStaffProfiles = StaffProfile::select('name','gender')->whereIn('id', $staffProfileIds)->get();

        return $otherStaffProfiles;
    }

    public function getgroupLeaderAttribute()
    {
        $groupLeaderId = GroupLeader::where('group_id', $this->group_id)->first();
        if ($groupLeaderId != null) {
            $groupLeaderName = StaffProfile::select('name')->where('id', $groupLeaderId->staff_profile_id)->first();
            return $groupLeaderName->name;
        } else {
            return null;
        }
    }
}
