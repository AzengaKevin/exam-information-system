<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsPayload = array(
            'Roles Browse',
            'Roles Read',
            'Roles Create',
            'Roles Update',
            'Roles Delete',

            'Permissions Browse',
            'Permissions Read',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',

            'Levels Browse',
            'Levels Read',
            'Levels Create',
            'Levels Update',
            'Levels Delete',

            'Streams Browse',
            'Streams Read',
            'Streams Create',
            'Streams Update',
            'Streams Delete',

            'Departments Browse',
            'Departments Read',
            'Departments Create',
            'Departments Update',
            'Departments Delete',

            'Subjects Browse',
            'Subjects Create',
            'Subjects Update',
            'Subjects Delete',

            'Teachers Browse',
            'Teachers Read',
            'Teachers Create',
            'Teachers Update',
            'Teachers Delete',

            'Guardians Browse',
            'Guardians Read',
            'Guardians Create',
            'Guardians Update',
            'Guardians Delete',

            'Users Browse',
            'Users Read',
            'Users Create',
            'Users Update',
            'Users Delete',

            'Departments Browse',
            'Departments Read',
            'Departments Create',
            'Departments Update',
            'Departments Delete',

            'Responsibilities Browse',
            'Responsibilities Read',
            'Responsibilities Create',
            'Responsibilities Update',
            'Responsibilities Update Locked',
            'Responsibilities Delete',
            'Responsibilities Delete Locked',

            'Exams Browse',
            'Exams Read',
            'Exams Create',
            'Exams Update',
            'Exams Delete',

            'Students Browse',
            'Students Read',
            'Students Create',
            'Students Update',
            'Students Delete',

            'LevelUnits Browse',
            'LevelUnits Read',
            'LevelUnits Create',
            'LevelUnits Update',
            'LevelUnits Delete',

            'Hostels Browse',
            'Hostels Read',
            'Hostels Create',
            'Hostels Update',
            'Hostels Delete',

            'Gradings Browse',
            'Gradings Read',
            'Gradings Create',
            'Gradings Update',
            'Gradings Delete'
        );

        array_walk($permissionsPayload, function($data){
            Permission::firstOrCreate(['name' => $data]);
        });

        /** @var Role */
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);

        $permissions = Permission::all(['id'])->pluck('id')->toArray();

        $adminRole->permissions()->attach($permissions);
    }
}
