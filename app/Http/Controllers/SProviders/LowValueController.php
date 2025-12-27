<?php

namespace App\Http\Controllers\SProviders;

use App\Http\Controllers\Controller;
use App\Http\Traits\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LowValueController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('LowValueRequest/Quotations');
    }

    public function show($id): Response
    {
        return Inertia::render('LowValueRequest/SendQuotation',['supply_id' => $id]);
    }
}
