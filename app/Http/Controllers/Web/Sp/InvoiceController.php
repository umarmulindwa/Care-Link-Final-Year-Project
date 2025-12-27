<?php

namespace App\Http\Controllers\Web\Sp;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryGroup;
use App\Models\BeneficiaryPayment;
use App\Models\Config;
use App\Models\DollarRate;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {

        return Inertia::render('features/sp/invoice/invoiceView', ['users' => request()->user]);
    }

    public function createInvoiceForm()
    {
        $user= auth()->user();
        $spProfile = ServiceProvider::where('email', $user->email)->latest()->first();
        $group = null;
        $payment = null;
        $group_id = null;
        $participants = [];
        $payment_id = null;

        if(User::find($user->id)->can('financial_service_provider')){

            $beneficiaryPaymentId = request()->query('payment_id');
            if(!is_null($beneficiaryPaymentId)){
               $payment =  BeneficiaryPayment::where('id',$beneficiaryPaymentId)->with(['beneficiaries' => function (HasMany $query) use ($beneficiaryPaymentId){
                    $query = $query->where('payment_status', "FSP Verified")->where('payment_id',$beneficiaryPaymentId);
                }])->first();
                $group = BeneficiaryGroup::with('provider')->find($payment->group_id);
                $participants = $payment->beneficiaries;


                if(count($participants)> 0){
                    $payment_id = $payment->id;
                }
            }

        }

        return Inertia::render('features/sp/invoice/invoiceForm', ['user' => $user,'beneficiary_payment_id'=>$payment_id, 'spProfile' => $spProfile,"beneficiary_group"=>$group, 'beneficiary_participants' => $participants]);
    }

    public function allInvoices()
    {
        return Inertia::render('features/sp/invoice/invoiceList', ['user' => request()->user]);
    }

    public function updateInvoice(Request $request, $id)
    {
        $vat = Config::first()->vat_percentage;
        $currencies = DollarRate::whereIn('currency', ['USD', 'ZAR', 'EUR', 'MWK'])->get();
        $spProfile = ServiceProvider::where('email', auth()->user()->email)->latest()->first();
        return Inertia::render('features/sp/invoice/invoiceForm', ['id' => $id, 'vat' => $vat, 'currencies' => $currencies, 'user' => auth()->user(), 'spProfile' => $spProfile])->with('id', $id);
    }

    public function statistics()
    {
        return Inertia::render('features/sp/invoice/statistics', ['user' => request()->user]);
    }
    public function testForm()
    {
        return Inertia::render("features/sp/invoice/testForm");
    }
}
