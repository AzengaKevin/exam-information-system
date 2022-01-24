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
            'Roles Manage Permissions',
            'Roles View Trashed',

            'Permissions Browse',
            'Permissions Read',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',
            'Permissions Restore',
            'Permissions Destroy',
            'Permissions View Trashed',
            
            'Levels Browse',
            'Levels Read',
            'Levels Create',
            'Levels Update',
            'Levels Delete',
            'Levels Restore',
            'Levels Destroy',
            'Levels View Trashed',
            'Levels Bulk Delete',

            'Streams Browse',
            'Streams Read',
            'Streams Create',
            'Streams Update',
            'Streams Delete',
            'Streams Restore',
            'Streams Destroy',
            'Streams View Trashed',
            'Streams Bulk Delete',

            'Departments Browse',
            'Departments Read',
            'Departments Create',
            'Departments Update',
            'Departments Delete',
            'Departments Restore',
            'Departments Destroy',
            'Departments View Trashed',

            'Subjects Browse',
            'Subjects Read',
            'Subjects Create',
            'Subjects Update',
            'Subjects Delete',
            'Subjects View Trashed',
            'Subjects Restore',
            'Subjects Destroy',
            'Subjects Bulk Delete',

            'Teachers Browse',
            'Teachers Read',
            'Teachers Create',
            'Teachers Update',
            'Teachers Delete',
            'Teachers Manage Responsibilities',
            'Teachers View Trashed',
            'Teachers Restore',
            'Teachers Destroy',
            
            'Guardians Browse',
            'Guardians Read',
            'Guardians Create',
            'Guardians Update',
            'Guardians Delete',
            'Guardians View Trashed',
            'Guardians Restore',
            'Guardians Destroy',

            'Users Browse',
            'Users Read',
            'Users Create',
            'Users Update',
            'Users Delete',
            'Users Restore',
            'Users Destroy',
            'Users View Trashed',
            'Users Bulk Update',

            'Responsibilities Browse',
            'Responsibilities Read',
            'Responsibilities Create',
            'Responsibilities Update',
            'Responsibilities Update Locked',
            'Responsibilities Delete',
            'Responsibilities Delete Locked',
            'Responsibilities Restore',
            'Responsibilities Destroy',
            'Responsibilities View Trashed',

            'Exams Browse',
            'Exams Read',
            'Exams Create',
            'Exams Update',
            'Exams Delete',
            'Exams Restore',
            'Exams Destroy',
            'Exams View Trashed',

            'Students Browse',
            'Students Read',
            'Students Create',
            'Students Update',
            'Students Delete',
            'Students Restore',
            'Students Destroy',
            'Students View Trashed',

            'Level Units Browse',
            'Level Units Read',
            'Level Units Create',
            'Level Units Update',
            'Level Units Delete',
            'Level Units Restore',
            'Level Units Destroy',
            'Level Units View Trashed',
            'Level Units Bulk Delete',
            
            'Hostels Browse',
            'Hostels Read',
            'Hostels Create',
            'Hostels Update',
            'Hostels Delete',
            'Hostels Restore',
            'Hostels Destroy',
            'Hostels View Trashed',
            
            'Gradings Browse',
            'Gradings Read',
            'Gradings Create',
            'Gradings Update',
            'Gradings Delete',
            'Gradings Restore',
            'Gradings Destroy',
            'Gradings View Trashed',

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

        $adminRole->permissions()->sync($permissions);
    }
}
