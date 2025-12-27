<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TravelPlanReviewsController extends Controller
{
    public function reviews($code): Response
    {
        return Inertia::render('TravelPlanner/Reviews',[
            'code' => $code,
        ]);
    }
}
