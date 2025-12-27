<?php

namespace App\Http\Controllers;

use App\Models\FieldOffice;
use Illuminate\Http\Request;
use App\Models\StaffProfile;
use App\Models\User;
use App\Models\Video;
use App\Models\Config;
use App\Models\District;
use App\Models\Bank;
use App\Models\Pillar;
use App\Models\Section;
use App\Models\OrganisationUnit;
use App\Models\Grade;
use App\Models\Category;
use App\Models\AppointmentType;
use App\Models\Room;
use App\Models\DollarRate;
use App\Models\DependantDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Donor;


class SystemConfiguration extends Controller
{
    public function systemconfig()
    {
        $systemVideos = Video::with(['views'])->latest()->get();
        $generalConfigs = Config::first();
        $districts = District::latest()->get();
        $banks = Bank::latest()->get();
        $pillars = Pillar::latest()->get();
        $sections = Section::latest()->get();
        $donors = Donor::latest()->get();
        $organisationUnit = OrganisationUnit::latest()->get();
        $grades = Grade::latest()->get();
        $categories = Category::latest()->get();
        $appointmentType = AppointmentType::latest()->get();
        $rooms = Room::latest()->get();
        $rates = DollarRate::latest()->get();
        $field_offices = FieldOffice::latest()->get();

        $staff = Cache::remember('config_unicef_staff', 30 * 60, function () {
            return User::query()
                ->whereHas('staffProfile', function ($query) {
                    $query->where('exited_unicef', 0);
                })
                ->select('id', 'name', 'email')
                ->get();
        });

        return Inertia::render(
            'SystemConfigurations/SystemConfigurations',
            [
                'systemVideos' => $systemVideos,
                'generalConfigs' => $generalConfigs,
                'districts' => $districts,
                'banks' => $banks,
                'pillars' => $pillars,
                'sections' => $sections,
                'organisationUnit' => $organisationUnit,
                'grade' => $grades,
                'categories' => $categories,
                'appointmentType' => $appointmentType,
                'rooms' => $rooms,
                'rates' => $rates,
                'donors'=> $donors,
                'field_offices' => $field_offices,
                'staff' => $staff,
            ]
        );
    }
    public function mobileApp(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (!$user->can('s_admin')) {
            return redirect()->back();
        }
        return Inertia::render('SystemConfigurations/MobileApp', [
            'user' => $user,
        ]);
    }
    /**
     * Get general configurations
     *
     * @return array
     */
    public function getGeneralConfiguration(Request $request)
    {
        $generalConfigs = Config::first();

        return apiResponse($generalConfigs);
    }
    public function addNewDonor(Request $request)
    {

        Donor::create([
            'donor_name' => $request->donor,
            'added_by' => auth()->user()->name,
            'added_by_email' => auth()->user()->email,
        ]);
        return apiResponse('success');
    }
    public  function submitGeneralConfig(Request $request)
    {

        //for low value threshold
        $low_value_theshold = [
            'invoice_currency' => $request->invoice_currency ?? 'USD',
            'invoice_value_amount' => $request->invoice_value_amount ?? 0.0,
        ];

        Config::updateOrCreate(
            ['id' => $request->id],
            [
                'office_location' => $request->office_location,
                'office_name' => $request->office_name,
                'platform_name' => $request->platform_name,
                'management_email' => $request->management_email,
                'bsc_email' => $request->bsc_email,
                'reminder_days' => $request->reminder_days,
                'leave_reminder_days' => $request->leave_reminder_days,
                'bsc_cit_approval_email' => $request->bsc_cit_approval_email,
                'bsc_task_assigner_email' => $request->bsc_task_assigner_email,
                'bsc_petty_cash_custodian' => null, // using permissions for this
                'vat_percentage' => $request->vat_percentage,
                'parking_booking_max_duration' => $request->parking_booking_max_duration,
                'identify_call_max_time' => $request->identify_call_max_time,
                'payroll_day' => $request->payroll_day,
                'dashboard_interval' => $request->dashboard_interval,
                'target_response_time' => $request->target_response_time,
                'maximum_records' => $request->maximum_records,
                'travel_deadline' => $request->travel_deadline,
                'all_staff_group_email' => $request->all_staff_group_email,
                'consultants_group_email' => $request->consultants_group_email,
                'transport_associate' => $request->transport_associate,
                'quaterly_asset_day' => $request->quaterly_asset_day,
                'call_tree_status_check_day' => $request->call_tree_status_reminder,
                'update_details_day' => $request->update_detail_reminder,
                'low_value_theshold' => json_encode($low_value_theshold),
                'maximum_pettycash' => $request->maximum_pettycash,
                'zain_AirtimeData_date' => $request->zainDate,
                'mtn_AirtimeDate_date' => $request->mtnDate,
                'passwords_to_check' => $request->passwords_to_check ?? 5,
                'password_expiry_days' => $request->password_expiry_days ?? 30,
            ]
        );

        return apiResponse('success');
    }
    public function addNewDistrict(Request $request)
    {

        District::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->district,
                'latitude' => $request->latitude,
                'longitude' => $request->longtitude
            ]
        );

        return apiResponse('success');
    }
    public function deleteDistrict(Request $request)
    {
        $district = District::find($request->id);
        $district->delete();
        return apiResponse('success');
    }
    public function addBank(Request $request)
    {
        Bank::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->bank,
            ]
        );

        return apiResponse('success');
    }
    public function deleteBank(Request $request)
    {
        $bank = Bank::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function deleteDonor(Request $request){
        $donor = Donor::find($request->id);
        $donor->delete();
        return apiResponse('success');
    }
    public function addNewPillar(Request $request)
    {
        Pillar::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->pillar,
            ]
        );

        return apiResponse('success');
    }
    public function deletePillar(Request $request)
    {
        $bank = Pillar::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addNewSection(Request $request)
    {
        Section::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->section,
            ]
        );

        return apiResponse('success');
    }
    public function deleteSection(Request $request)
    {
        $bank = Section::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addNewOrganisaitionUnit(Request $request)
    {
        OrganisationUnit::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->unit,
            ]
        );

        return apiResponse('success');
    }
    public function deleteOrganisationUnit(Request $request)
    {
        $bank = OrganisationUnit::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addNewGrade(Request $request)
    {
        Grade::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->grade,
            ]
        );

        return apiResponse('success');
    }
    public function deleteGrade(Request $request)
    {
        $bank = Grade::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addNewCategory(Request $request)
    {
        Category::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->category,
            ]
        );

        return apiResponse('success');
    }
    public function deleteCategory(Request $request)
    {
        $bank = Category::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addNewAppointmentType(Request $request)
    {
        AppointmentType::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->appointment,
            ]
        );

        return apiResponse('success');
    }
    public function deleteAppointmentType(Request $request)
    {
        $bank = AppointmentType::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addNewRoom(Request $request)
    {
        Room::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->room,
                'office_location' => $request->roomOfficeLocation,
                'office_location_coords' => $request->roomOfficeLocationCoords
            ]
        );

        return apiResponse('success');
    }
    public function deleteRoom(Request $request)
    {
        $bank = Room::find($request->id);
        $bank->delete();
        return apiResponse('success');
    }
    public function addDollarRate(Request $request)
    {

        DollarRate::updateOrCreate(
            ['id' => $request->id],
            [
                "rate" => $request->rate,
                "currency" => $request->exchange,
                "source" => DollarRate::MANUAL,
            ]
        );

        return apiResponse('success');
    }
    public function deleteRate(Request $request)
    {
        $rate = DollarRate::find($request->id);
        $rate->delete();
        return apiResponse('success');
    }
    public function addDependant(Request $request)
    {

        $dependant = DependantDetail::updateOrCreate(
            ['id' => $request->id],
            [
                "name" => $request->name,
                "sex" => $request->gender,
                "date_of_birth" => $request->date,
                "staff_id" => auth()->user()->id
            ]
        );

        if($request->hasFile('pp_file')){
            $document_file = $request->file('pp_file');

            //if being edited
            if($request->id){
                $previous = $dependant->documents()->where('file_name','dependant passport')->first();
                if($previous){
                    Storage::delete(str_replace('storage', 'public',$previous->file_path));
                    $previous->delete();
                }
            }

            $path = $document_file->store('/public/dependants');
            $dependant->documents()->create([
                'staff_id' => auth()->user()->staff_profile->id,
                'file_name' => 'dependant passport',
                'file_path' => str_replace('public', '/storage', $path),
                'file_type' => $document_file->getMimeType(),
                'file_size' => $document_file->getSize(),
            ]);
        }
        // return apiResponse('success');
    }
    public function removeDependant(Request $request)
    {
        $dependant = DependantDetail::find($request->id);
        $dependant->delete();
        return apiResponse('success');
    }

    public function getLatestForexRates()
    {
        // UN Treasury web scrapper
        Artisan::call('forex-rate:fetch');
    }


    public function addFieldOffice(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_headquarters' => 'nullable|boolean',
            'field_chief' => 'nullable|integer'
        ]);

        $field_office = FieldOffice::query()->updateOrCreate(['id' => $validated['id']], $validated);

        if ($validated['is_headquarters']) {
            FieldOffice::query()
                ->whereNot('id', $field_office->id)
                ->where('is_headquarters', 1)
                ->update(['is_headquarters' => 0]);
        }


        if ($validated['field_chief']) {
            $permission = "field_office_chief_" . strtolower($validated['name']);
            $user = User::query()->find($validated['field_chief']);

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            if ($user && Permission::query()->where('name', $permission)->exists()) {
                $user->givePermissionTo($permission);
            }
        }


        return apiResponse('success');
    }

    public function getFieldOffices()
    {
        try {
            $search = request()->query('search');
            $results = FieldOffice::whereAny(['name', 'location'], "like", "%" . $search . "%")->get();
            return apiResponse($results);
        } catch (\Throwable $th) {
            return apiErrorResponse($th->getMessage(), ['error' => $th->getTrace()]);
        }
    }

    public function deleteFieldOffice(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer'
        ]);

        $field_office = FieldOffice::query()->find($validated['id']);

        if (!$field_office) return null;

        //delete associated permission
        $permissions = [
            'field_office_chief_' . $field_office->name,
            'transport_associate_' . $field_office->name,
        ];

        Permission::query()->whereIn('name', $permissions)->delete();
        $field_office->delete();

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return apiResponse('success');
    }
}
