<?php

namespace App\Http\Controllers;
use App\Models\IncidentLocation;
use App\Models\Section;
use App\Models\Incident;
use App\Models\ServiceProvider;
use App\Models\Donor;

use Illuminate\Http\Request;

class IncidenReporting extends Controller
{
    public function index()
    {
        $incidents = [];
        $incidents = Incident::where('status', '!=', 'deleted')->where('reported_by_email', auth()->user()->email)->latest()->get();
        
        return \Inertia\Inertia::render('IncidentReporting/MainPage', [
            'incidents' => $incidents
        ]);
    }
    public function createIncident(){
        $incidents = [];
        $implementingPartners = [];

        
        //getting Incident locations
        $incidentLocations = IncidentLocation::select('id', 'state', 'county', 'site_name', 'latitude', 'cooperating_partner', 'payam', 'longitude', 'comment')->get();
        $sections = Section::select('name', 'id')->get();


        return \Inertia\Inertia::render('IncidentReporting/CreateIncident', [
            'implementingPartners' => $implementingPartners,
            'incidentLocations' => $incidentLocations,
            'sections' => $sections,

        ]);
    }
    public function registerSignature(){

        return \Inertia\Inertia::render('IncidentReporting/SignatureRegister', [
       

        ]);
    }
    public function reviewIncident(Request $request)
    {
        $incident = Incident::where('id', $request->id)->with('logs', 'IncidentSupply', 'IncidentTimeline', 'AffectedEntity', 'comments')->first();
        $implementingPartners = ServiceProvider::select('id', 'user_id', 'email', 'name')->get();
        $donors = Donor::select('id', 'donor_name')->latest()->get();
        $incidentLocations = IncidentLocation::select('id', 'state', 'county', 'site_name', 'latitude', 'cooperating_partner', 'payam', 'longitude', 'comment')->get();

        return \Inertia\Inertia::render('IncidentReporting/IncidentReview', [
            'incident' => $incident,
            'donors' => $donors,
            'implementingPartners' => $implementingPartners,
            'incidentLocations' => $incidentLocations,
        ]);
    }
}
