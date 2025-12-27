<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarSchedule extends Model
{
    protected $fillable = [
        'staff_profile_id',
        'platform',
        'title',
        'description',
        'scheduled_date',
        'scheduled_time_start',
        'scheduled_time_end',
        'schedule_uuid', //this field will be used to group schedules (know who else has the same schedule)
        'created_by_id', //StaffProfileId of the user who created the schedule. ONLY UNICEF STAFF SHOULD CREATE SCHEDULE
        'metadata', //Any other data associated to the schedule
        'is_accepted'
    ];

    protected $appends = ['similar_staff_schedule', 'organizer'];

    public function getSimilarStaffScheduleAttribute()
    {
        return  self::where('schedule_uuid', $this->schedule_uuid)->get()->map(function ($schedule) {
            return StaffProfile::find($schedule->staff_profile_id);
        })->unique()->values()->all();
    }

    public function getOrganizerAttribute()
    {
        return StaffProfile::find($this->created_by_id);
    }
}
