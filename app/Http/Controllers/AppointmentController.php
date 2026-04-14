<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;


class AppointmentController extends Controller
{
     public function myAppointments(Request $request)
    {
        return Inertia::render('Appointments/MyAppointments', []);
    }
    public function scheduleAppointments(Request $request)
    {
        return Inertia::render('Appointments/ScheduleAppointments', []);
    }
}
