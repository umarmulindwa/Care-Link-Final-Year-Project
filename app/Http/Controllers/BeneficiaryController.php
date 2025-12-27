<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BeneficiaryController extends Controller
{
    public function uploadBeneficiaries(){
        return Inertia::render('features/beneficiaries/masterView',[
            'users'=>auth()->user(),
        ]);
    }

    public function ipViewBeneficiaries($id){
        return Inertia::render('features/beneficiaries/ipViewBeneficiaries',[
            'group_id'=>$id
        ]);
    }

    public function reconcileBeneficiaries(){
        if(User::find(auth()->user()->id)->can('financial_service_provider')){
            return Inertia::render('features/beneficiaries/reconcile',[
                'users'=>auth()->user(),
            ]);
        }
        return redirect()->route('dashboard');

    }

   public function reviewBeconcileBeneficiaries($id){

    if(User::find(auth()->user()->id)->can('financial_service_provider')){
        return Inertia::render('features/beneficiaries/beneficiaryReconcileParticipantsList',[
            'users'=>auth()->user(),
            'payment_id'=>$id
        ]);
    }
    return redirect()->route('dashboard');

   }

  public function viewAttendance($id){
    return Inertia::render('features/beneficiaries/attendance/Index',[
        'group_id'=>$id
    ]);

  }
}
