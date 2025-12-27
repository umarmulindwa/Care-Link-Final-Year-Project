<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaceFormController extends Controller
{
    //

    public function createFace() {
        $spProfile = ServiceProvider::where('email', auth()->user()->email)->latest()->first();

        return Inertia::render("FaceForms/createFaceForm",['profile'=>$spProfile]);
    }
    public function listFace() {
        return Inertia::render("FaceForms/ListFaceForms",[]);
    }
    public function reportsFace() {
        return Inertia::render("FaceForms/ReportView",[]);
    }
}
