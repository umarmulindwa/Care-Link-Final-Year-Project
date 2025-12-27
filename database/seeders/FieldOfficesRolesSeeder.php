<?php

namespace Database\Seeders;

use App\Models\FieldOffice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FieldOfficesRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::all();

        foreach($permissions as $permission){
            preg_match('/\d+/', $permission->description, $matches);

            if (isset($matches[0])){
                $fieldOffice = FieldOffice::query()->where('id',$matches[0])->first();

                if($fieldOffice){
                    if(str_contains($permission->description,"Transport Associate ")){

                        $role_name = 'Transport Associate('.$fieldOffice->name.')';

                        if(!Role::where('name',$role_name)->exists()){
                            $role = Role::create([
                                'name' => $role_name,
                                'guard_name' => 'web',
                            ]);
                            $role->givePermissionTo($permission);
                        }
                    }

                    if(str_contains($permission->description,"Field Office Chief")){

                        $role_name = 'Field Office Chief('.$fieldOffice->name.')';

                        if(!Role::where('name',$role_name)->exists()){
                            $role = Role::create([
                                'name' => $role_name,
                                'guard_name' => 'web',
                            ]);
                            $role->givePermissionTo($permission);
                        }
                    }
                }

            }
        }
    }
}
