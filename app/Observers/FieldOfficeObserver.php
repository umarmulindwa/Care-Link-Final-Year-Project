<?php

namespace App\Observers;

use App\Models\FieldOffice;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 *
 */
class FieldOfficeObserver
{
    /**
     * @param FieldOffice $fieldOffice
     * @return void
     */
    public function created(FieldOffice $fieldOffice): void
    {
        $this->updateOrCreate($fieldOffice);
    }

    /**
     * @param FieldOffice $fieldOffice
     * @return void
     */
    public function updated(FieldOffice $fieldOffice): void
    {
        $this->updateOrCreate($fieldOffice);
    }

    /**
     * @param FieldOffice $fieldOffice
     * @return void
     */
    public function updateOrCreate(FieldOffice $fieldOffice): void
    {
        $permissions = [
            [
                'type' => 'Transport Associate',
                'prefix' => 'transport_associate_',
                'description' => "Transport Associate Field Office ID {$fieldOffice->id}"
            ],
            [
                'type' => 'Field Office Chief',
                'prefix' => 'field_office_chief_',
                'description' => "Field Office Chief ID {$fieldOffice->id}"
            ]
        ];

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($permissions as $permission) {
            $old_permission = Permission::where('description', $permission['description'])->first();
            $new_permission_name = $permission['prefix'] . strtolower($fieldOffice->name);

            if ($old_permission) {
                // Check if a permission with the new name already exists
                if (!Permission::where('name', $new_permission_name)->exists()) {
                    $old_permission->name = $new_permission_name;
                    $old_permission->save();
                }
            } else {
                if (!Permission::where('name', $new_permission_name)->exists()) {
                    $new_permission = Permission::create([
                        'name' => $new_permission_name,
                        'guard_name' => 'web',
                        'description' => $permission['description'],
                    ]);

                    $role_name = $permission['type'].'('.$fieldOffice->name.')';

                    if(!Role::where('name',$role_name)->exists()){
                        $role = Role::create([
                            'name' => $role_name,
                            'guard_name' => 'web',
                        ]);
                        $role->givePermissionTo($new_permission);
                    }
                }
            }
        }
    }
}
