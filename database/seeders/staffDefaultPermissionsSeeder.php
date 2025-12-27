<?php

namespace Database\Seeders;

use App\Models\StaffProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use SebastianBergmann\Diff\Exception;

class staffDefaultPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = StaffProfile::query()->get();
        $staff->each(function($query){
           $user = \App\Models\User::query()->where('email',$query->email)->select('id')->first();
           $permissions = $this->get_applicable_permissions($query->position_text);

           if(count($permissions) > 0){
               try{
                   $user->givePermissionTo($permissions);
               }catch (Exception $exception){
                   print_r(['Error' => $exception]);
               }

           }
        });
    }

    public function get_applicable_permissions($position): array
    {
        //known positions
        $objectArray = [
            (object)["title" => "Driver", "permissions" => ['driver']],
            (object)["title" => "Senior Administrative Associate", "permissions" => ['senior_admin_associate']],
            (object)["title" => "Travel Associate", "permissions" => ['transport_associate']],
            (object)["title" => "Senior Driver", "permissions" => ['driver']],
            (object)["title" => "Administrative Assistant", "permissions" => ['admin_assistant']],
            (object)["title" => "Administrative & Finance Manager", "permissions" => []],
            (object)["title" => "Administrative Specialist", "permissions" => []],
            (object)["title" => "Chief Child Protection", "permissions" => ['chief_child']],
            (object)["title" => "Child Protection Specialist", "permissions" => []],
            (object)["title" => "Child Protection Officer", "permissions" => []],
            (object)["title" => "Programme Associate", "permissions" => []],
            (object)["title" => "Chief of Communication", "permissions" => ['chief_communications']],
            (object)["title" => "Communication Specialist", "permissions" => []],
            (object)["title" => "Education Officer", "permissions" => []],
            (object)["title" => "Chief Education", "permissions" => ['chief_education/adolescents']],
            (object)["title" => "Education Specialist", "permissions" => []],
            (object)["title" => "Education Manager", "permissions" => []],
            (object)["title" => "Chief Field Operations", "permissions" => []],
            (object)["title" => "Nutrition Cluster Coordinator", "permissions" => []],
            (object)["title" => "Emergency Officer", "permissions" => []],
            (object)["title" => "Emergency Specialist", "permissions" => []],
            (object)["title" => "Information Management Specialist", "permissions" => []],
            (object)["title" => "Child Protection Cluster Coordinator", "permissions" => []],
            (object)["title" => "Senior Finance Associate", "permissions" => []],
            (object)["title" => "Finance Associate", "permissions" => []],
            (object)["title" => "Senior Finance Assistant", "permissions" => []],
            (object)["title" => "Finance Specialist", "permissions" => []],
            (object)["title" => "Representative", "permissions" => ['rep']],
            (object)["title" => "Project Associate", "permissions" => []],
            (object)["title" => "Programme Officer", "permissions" => []],
            (object)["title" => "Chief Field Office", "permissions" => ['field_office_chief']],
            (object)["title" => "Deputy Representative Programme", "permissions" => ['dep_rep_prog']],
            (object)["title" => "Implementing Partnership Mgmt Specialist", "permissions" => []],
            (object)["title" => "Implementing Partnership Mgmt Officer", "permissions" => []],
            (object)["title" => "Senior Programme Specialist", "permissions" => []],
            (object)["title" => "Health Officer", "permissions" => []],
            (object)["title" => "Immunization Manager", "permissions" => []],
            (object)["title" => "Cold Chain Officer", "permissions" => []],
            (object)["title" => "Immunization Specialist", "permissions" => []],
            (object)["title" => "Cold Chain Specialist", "permissions" => []],
            (object)["title" => "Human Resources Specialist", "permissions" => ['hr_staff']],
            (object)["title" => "Human Resources Officer", "permissions" => ['hr_staff']],
            (object)["title" => "Senior Human Resources Associate", "permissions" => []],
            (object)["title" => "Human Resources Associate", "permissions" => ['hr_staff']],
            (object)["title" => "Information Comm. Technology Officer", "permissions" => ['ict_officer']],
            (object)["title" => "Information Comm. Technology Associate", "permissions" => ['ict_assoc']],
            (object)["title" => "Senior Info. Comm. Technology Associate", "permissions" => ['snr_ict_assc']],
            (object)["title" => "Information Comm. Technology Manager", "permissions" => []],
            (object)["title" => "Nutrition Officer", "permissions" => []],
            (object)["title" => "Chief Nutrition", "permissions" => ['chief_nutrition']],
            (object)["title" => "Nutrition Manager", "permissions" => []],
            (object)["title" => "Deputy Representative Operations", "permissions" => ['dep_rep_ops']],
            (object)["title" => "Operations Associate", "permissions" => []],
            (object)["title" => "Logistics Officer", "permissions" => []],
            (object)["title" => "Administrative Associate", "permissions" => ['admin_Assc']],
            (object)["title" => "Warehouse Assistant", "permissions" => []],
            (object)["title" => "Wash Officer", "permissions" => []],
            (object)["title" => "Senior Security Associate", "permissions" => []],
            (object)["title" => "Radio Operator", "permissions" => []],
            (object)["title" => "Security Associate", "permissions" => []],
            (object)["title" => "Chief Planning, Monitoring & Evaluation", "permissions" => []],
            (object)["title" => "Planning, Monitoring & Evaluation Specialist", "permissions" => []],
            (object)["title" => "Monitoring & Evaluation Officer", "permissions" => []],
            (object)["title" => "Social & Behavior Change Officer", "permissions" => []],
            (object)["title" => "Social & Behavior Change Specialist", "permissions" => []],
            (object)["title" => "Chief Social & Behavior Change", "permissions" => []],
            (object)["title" => "Security Officer", "permissions" => ['a_sec']],
            (object)["title" => "Staff Security Specialist", "permissions" => []],
            (object)["title" => "Administrative Assistant Security", "permissions" => []],
            (object)["title" => "Social Policy Specialist", "permissions" => []],
            (object)["title" => "Supply & Logistics Manager", "permissions" => []],
            (object)["title" => "Logistics Specialist", "permissions" => []],
            (object)["title" => "Supply Associate", "permissions" => ['supply_staff','supply']],
            (object)["title" => "Supply Officer", "permissions" => ['supply_staff','supply','supply_officer']],
            (object)["title" => "Senior Warehouse Associate", "permissions" => []],
            (object)["title" => "Supply Specialist", "permissions" => []],
            (object)["title" => "Supply Assistant", "permissions" => []],
            (object)["title" => "Senior Logistics Associate", "permissions" => []],
            (object)["title" => "WASH Specialist", "permissions" => []],
            (object)["title" => "Chief WASH", "permissions" => []],
            (object)["title" => "WASH Manager", "permissions" => []],
            (object)["title" => "Construction Engineer", "permissions" => []],
        ];

        $permissions = array_map(function($object) {
            return $object->permissions;
        }, array_filter($objectArray, function($object) use ($position) {
            return $object->title === $position;
        }));

        return array_merge(...$permissions);

    }
}
