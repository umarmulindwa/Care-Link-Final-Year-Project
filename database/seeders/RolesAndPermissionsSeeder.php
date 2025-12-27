<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //System Permissions
        /**
         * NOTE:
         * Add new permission at the end of the array
         */
        $permissions = [
            /**
             * NOTE:
             * In order to Avoid duplicate permissions, please check whether the permission you want to add already exists
             * if not, add a comment describing that permission.
             */
            'bsc', // v1 permission
            'c_bsc', // v1 permission (chief of BSC)
            'a_sec', // v1 permission
            'forms', // v1 permission
            'f_tat', // v1 permission
            'supply', // v1 permission
            'hr', // v1 permission
            'mng_staff', // v1 permission
            'm_staff', // v1 permission
            'm_wbs', // v1 permission
            'leave', //leave permission
            'admin', // permission for a administration staff
            's_admin', // permission for a super Admin
            'section_chief', //permission for a sections chief (NOTE: section chiefs are described by roles but every section chief role should have this permission)
            'admin_Assc', //permission for administrative associate
            'admin_officer', //permission for admin officer
            'snr_ict_assc', //permission given to the senior ict associate
            'ops_chief', //permission for chief of operations
            'transport_associate', // permission for a transport associate
            'intern',
            'volunteer',
            'executive_associate',
            'hr_staff', // This is a permission given to all staff in HR
            'sp', //This is the permission for the service provider
            'dep_rep', // This is the permission that is given to the deputy representative
            'rep', // This is the permission for the representative
            'c_finance', //This is the role given to the chief of finance
            'pc_custodian', // This is the permission given to the pettycash custodian
            'finance_staff', // This is the permission given to the finance staff
            'supply_staff', // this is the permission that is given to supply staff
            'dep_rep_ops',
            'dep_rep_prog',
            'supply_officer',
            'senior_admin_associate',
            'ict_assoc',
            'driver',
            'ict_officer',
            'admin_assistant',
            'psb_s',
            'fuel_card_custodian', //fuel_card_custodians
            'hr_business_partner',
            'staff_assoc', //Staff Association
            'field_office_chief', //note that this is not section chief permission.
            'admin_staff', // This permission is given to staff in Administration
            'hr_specialist', // HR Specialist, used in the Vision Access & Services Access System
            'qa_manager', // HACT / Quality Assurance Manager
            'ict_specialist',
            'admin_specialist', // permission given to the admin specialist
            'budget_officer', // permission for the budget officer
            'financial_service_provider', // for the financial service provider
            'beneficiary_service_provider', // for the beneficiary service provider
            'donor_relations', //Permission given to the Donor relations
            'airtime_admin_emails', //Staff with this permission receive administration airtime and data emails.
            'prog_specialist' // This is the permission given to the program specialist
        ];

        //current permissions
        $activePermissions = Permission::select('name')->get()->toArray();
        $tempPermArray = [];
        foreach ($activePermissions as $activePermission) {
            array_push($tempPermArray, $activePermission['name']);
        }

        //re-create permissions from the array
        foreach ($permissions as $permission) {
            if (!in_array($permission, $tempPermArray)) {
                Permission::create(['name' => $permission], ['name' => $permission]);
            }
        }
    }
}
