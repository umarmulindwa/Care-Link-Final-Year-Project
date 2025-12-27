<?php

namespace App\Http\Controllers\SProviders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleHireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('VehicleHire/List');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($trip_id,$vhr_id,$vehicle)
    {
        return Inertia::render('VehicleHire/MakeOffer',[
            'trip_id' => $trip_id,
            'vhr_id' => $vhr_id,
            'vehicle' => $vehicle
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $month)
    {
        return Inertia::render('VehicleHire/Requests',[
            'request_id' => $id,
            'month' => $month,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
