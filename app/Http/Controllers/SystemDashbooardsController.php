<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemDashbooardsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //seconds
        $dashboard_change_intervals = Config::query()->value('dashboard_interval') ?? 60;

        //to microseconds
        $dashboard_change_intervals = $dashboard_change_intervals * 1000;
        return Inertia::render('Dashboards/main',compact('dashboard_change_intervals'));
    }
}
