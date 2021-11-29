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
            'Roles Create',
            'Roles Update',
            'Roles Delete',

            'Permissions Browse',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',

            'Levels Browse',
            'Levels Create',
            'Levels Update',
            'Levels Delete',

            'Streams Browse',
            'Streams Create',
            'Streams Update',
            'Streams Delete',

            'Departments Browse',
            'Departments Create',
            'Departments Update',
            'Departments Delete',

            'Subjects Browse',
            'Subjects Create',
            'Subjects Update',
            'Subjects Delete',

            'Teachers Browse',
            'Teachers Create',
            'Teachers Update',
            'Teachers Delete',

            'Guardians Browse',
            'Guardians Create',
            'Guardians Update',
            'Guardians Delete',

            'Users Browse',
            'Users Create',
            'Users Update',
            'Users Delete',

            'Departments Browse',
            'Departments Create',
            'Departments Update',
            'Departments Delete',

            'Responsibilities Browse',
            'Responsibilities Create',
            'Responsibilities Update',
            'Responsibilities Delete',

            'Exams Browse',
            'Exams Create',
            'Exams Update',
            'Exams Delete',

            'Student Browse',
            'Student Create',
            'Student Update',
            'Student Delete',

            'LevelUnits Browse',
            'LevelUnits Create',
            'LevelUnits Update',
            'LevelUnits Delete'
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
