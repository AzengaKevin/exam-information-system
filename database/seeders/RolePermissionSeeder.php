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
            'Roles Restore',
            'Roles Destroy',

            'Permissions Browse',
            'Permissions Read',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',
            'Permissions Restore',
            'Permissions Destroy',

            'Levels Browse',
            'Levels Read',
            'Levels Create',
            'Levels Update',
            'Levels Delete',
            'Levels Restore',
            'Levels Destroy',

            'Streams Browse',
            'Streams Read',
            'Streams Create',
            'Streams Update',
            'Streams Delete',
            'Streams Restore',
            'Streams Destroy',

            'Departments Browse',
            'Departments Read',
            'Departments Create',
            'Departments Update',
            'Departments Delete',
            'Departments Restore',
            'Departments Destroy',

            'Subjects Browse',
            'Subjects Create',
            'Subjects Update',
            'Subjects Delete',
            'Subjects Restore',
            'Subjects Destroy',

            'Teachers Browse',
            'Teachers Read',
            'Teachers Create',
            'Teachers Update',
            'Teachers Delete',
            'Teachers Manage Responsibilities',
            'Teachers Restore',
            'Teachers Destroy',

            'Guardians Browse',
            'Guardians Read',
            'Guardians Create',
            'Guardians Update',
            'Guardians Delete',
            'Guardians Restore',
            'Guardians Destroy',

            'Users Browse',
            'Users Read',
            'Users Create',
            'Users Update',
            'Users Delete',
            'Users Restore',
            'Users Destroy',

            'Responsibilities Browse',
            'Responsibilities Read',
            'Responsibilities Create',
            'Responsibilities Update',
            'Responsibilities Update Locked',
            'Responsibilities Delete',
            'Responsibilities Delete Locked',
            'Responsibilities Restore',
            'Responsibilities Destroy',

            'Exams Browse',
            'Exams Read',
            'Exams Create',
            'Exams Update',
            'Exams Delete',
            'Exams Restore',
            'Exams Destroy',

            'Students Browse',
            'Students Read',
            'Students Create',
            'Students Update',
            'Students Delete',
            'Students Restore',
            'Students Destroy',

            'LevelUnits Browse',
            'LevelUnits Read',
            'LevelUnits Create',
            'LevelUnits Update',
            'LevelUnits Delete',
            'LevelUnits Restore',
            'LevelUnits Destroy',

            'Hostels Browse',
            'Hostels Read',
            'Hostels Create',
            'Hostels Update',
            'Hostels Delete',
            'Hostels Restore',
            'Hostels Destroy',

            'Gradings Browse',
            'Gradings Read',
            'Gradings Create',
            'Gradings Update',
            'Gradings Delete',
            'Gradings Restore',
            'Gradings Destroy',

            'Grades Browse',
            'Grades Read',
            'Grades Create',
            'Grades Update',
            'Grades Delete',
            'Grades Restore',
            'Grades Destroy',

            'Files Browse',
            'Files Read',
            'Files Create',
            'Files Update',
            'Files Delete',
            'Files Restore',
            'Files Destroy',

            'Messages Browse',
            'Messages Read',
            'Messages Create',
            'Messages Update',
            'Messages Delete',
            'Messages Restore',
            'Messages Destroy',
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
