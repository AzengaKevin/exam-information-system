<?php

namespace App\Actions\Roles;

use App\Models\Permission;
use App\Models\Responsibility;
use App\Models\Role;

class GetAppropriateRole
{
    private const DOS_DEFAULT_PERMISSION = [

        'Dashboard Access',

        'Levels Browse',
        'Levels Read',

        'Level Units Browse',
        'Level Units Read',

        'Subjects Browse',
        'Subjects Read',

        'Teachers Browse',
        'Teachers Read',

        'Responsibilities Browse',
        'Responsibilities Read',

        'Exams Browse',
        'Exams Read',
        'Exams Create',
        'Exams Update',
        'Exams Delete',

        'Students Browse',
        'Students Read',
            
        'Gradings Browse',
        'Gradings Read',

        'Grades Browse',
        'Grades Read',

        'Messages Browse',
        'Messages Read',
        'Messages Create'
        
    ];

    private const SUBJECT_DEFAULT_PERMISSION = [
        
        'Dashboard Access',
        'Levels Browse',
        'Levels Read',

        'Level Units Browse',
        'Level Units Read',

        'Subjects Browse',
        'Subjects Read',

        'Teachers Browse',

        'Exams Browse',
        'Exams Read',

        'Students Browse',
        'Students Read',
            
        'Gradings Browse',
        'Gradings Read',

        'Grades Browse',
        'Grades Read',

        'Messages Browse',
        'Messages Read'
        
    ];

    private const CLASS_TEACHER_DEFAULT_PERMISSION = [

        'Dashboard Access',

        'Levels Browse',
        'Levels Read',

        'Level Units Browse',
        'Level Units Read',

        'Subjects Browse',
        'Subjects Read',

        'Teachers Browse',
        'Teachers Read',

        'Responsibilities Browse',
        'Responsibilities Read',

        'Exams Browse',
        'Exams Read',

        'Students Browse',
        'Students Read',
            
        'Gradings Browse',
        'Gradings Read',

        'Grades Browse',
        'Grades Read',

        'Messages Browse',
        'Messages Read',
        'Messages Create'
        
    ];

    private const LEVEL_SUPERVISOR_DEFAULT_PERMISSION = [

        'Dashboard Access',

        'Levels Browse',
        'Levels Read',

        'Level Units Browse',
        'Level Units Read',

        'Subjects Browse',
        'Subjects Read',

        'Teachers Browse',
        'Teachers Read',

        'Responsibilities Browse',
        'Responsibilities Read',

        'Exams Browse',
        'Exams Read',

        'Students Browse',
        'Students Read',
            
        'Gradings Browse',
        'Gradings Read',

        'Grades Browse',
        'Grades Read',

        'Messages Browse',
        'Messages Read',
        'Messages Create'
        
    ];

    private const DEPUTY_DEFAULT_PERMISSION = [

        'Dashboard Access',

        'Levels Browse',
        'Levels Read',
        'Levels Update',

        'Streams Browse',
        'Streams Read',
        'Streams Create',
        'Streams Update',

        'Departments Browse',
        'Departments Read',
        'Departments Create',
        'Departments Update',
        'Departments Delete',

        'Level Units Browse',
        'Level Units Read',
        'Level Units Create',
        'Level Units Update',

        'Subjects Browse',
        'Subjects Read',
        'Subjects Create',
        'Subjects Update',
        'Subjects Delete',

        'Teachers Browse',
        'Teachers Read',
        'Teachers Update',
        'Teachers Manage Responsibilities',
            
        'Guardians Browse',
        'Guardians Read',
        'Guardians Create',
        'Guardians Update',
        'Guardians Delete',

        'Responsibilities Browse',
        'Responsibilities Read',
        'Responsibilities Create',
        'Responsibilities Update',

        'Exams Browse',
        'Exams Read',

        'Students Browse',
        'Students Read',
        'Students Create',
        'Students Update',
        'Students Delete',
            
        'Gradings Browse',
        'Gradings Read',

        'Grades Browse',
        'Grades Read',

        'Messages Browse',
        'Messages Read',
        'Messages Create'
        
    ];


    private const SCHOOL_HEAD_DEFAULT_PERMISSION = [

        'Dashboard Access',
        'Settings View',
        'General Settings View',
        'Settings Update',

        'Roles Browse',
        'Roles Read',

        'Permissions Browse',
        'Permissions Read',

        'Levels Browse',
        'Levels Read',

        'Streams Browse',
        'Streams Read',

        'Departments Browse',
        'Departments Read',

        'Level Units Browse',
        'Level Units Read',

        'Subjects Browse',
        'Subjects Read',

        'Teachers Browse',
        'Teachers Read',
            
        'Guardians Browse',
        'Guardians Read',

        'Responsibilities Browse',
        'Responsibilities Read',
            
        'Hostels Browse',
        'Hostels Read',

        'Exams Browse',
        'Exams Read',

        'Students Browse',
        'Students Read',
            
        'Gradings Browse',
        'Gradings Read',

        'Grades Browse',
        'Grades Read',

        'Messages Browse',
        'Messages Read',
        
    ];
    
    /**
     * Get appropriate role for the responsibility
     * 
     * @return Role
     */
    public static function getRole(Responsibility $responsibility)
    {

        /** @var Role */
        $role = Role::where('name', $responsibility->name)->first();

        if(is_null($role)){

            $resName = $responsibility->name;

            switch ($resName) {

                case 'Deputy':
                    $role = self::createDeputyRole($responsibility->name);
                    break;
                case 'Director of Studies': case 'Exam Teacher':
                    $role = self::createDirectorOfStudiesRole($responsibility->name);
                    break;
                case 'Principal': case 'Head Teacher':
                    $role = self::createSchoolHeadRole($responsibility->name);
                    break;
                case 'Level Supervisor':
                    $role = self::createLevelSupervisorRole($responsibility->name);
                    break;
                case 'Class Teacher':
                    $role = self::createClassTeacherRole($responsibility->name);
                    break;
                case 'Subject Teacher':
                    $role = self::createSubectTeacherRole($responsibility->name);
                    break;
                default:
                    $role = Role::firstOrCreate(['name' => $responsibility->name]);
                    break;
            }
        }

        return $role;
        
    }

    /**
     * Defines the head teacher role
     * 
     * @return Role
     */
    private static function createSchoolHeadRole(string $name)
    {
        /** @var Role */
        $role = Role::firstOrCreate(['name' => $name]);

        $permissions = Permission::whereIn('name', self::SCHOOL_HEAD_DEFAULT_PERMISSION)
            ->get(['id'])->pluck('id')->all();

        $role->permissions()->sync($permissions);

        return $role;
    }

    /**
     * Defines the subject teacher role
     * 
     * @return Role
     */
    private static function createSubectTeacherRole(string $name)
    {
        /** @var Role */
        $role = Role::firstOrCreate(['name' => $name]);

        $permissions = Permission::whereIn('name', self::SUBJECT_DEFAULT_PERMISSION)
            ->get(['id'])->pluck('id')->all();

        $role->permissions()->sync($permissions);

        return $role;
    }

    /**
     * Defines the class teacher role
     * 
     * @return Role
     */
    private static function createClassTeacherRole(string $name)
    {
        /** @var Role */
        $role = Role::firstOrCreate(['name' => $name]);

        $permissions = Permission::whereIn('name', self::CLASS_TEACHER_DEFAULT_PERMISSION)
            ->get(['id'])->pluck('id')->all();

        $role->permissions()->sync($permissions);

        return $role;
    }

    /**
     * Defines the level supervisor role
     * 
     * @return Role
     */
    private static function createLevelSupervisorRole(string $name)
    {
        /** @var Role */
        $role = Role::firstOrCreate(['name' => $name]);

        $permissions = Permission::whereIn('name', self::LEVEL_SUPERVISOR_DEFAULT_PERMISSION)
            ->get(['id'])->pluck('id')->all();

        $role->permissions()->sync($permissions);

        return $role;
    }

    /**
     * Defines the director of studies role
     * 
     * @return Role
     */
    private static function createDirectorOfStudiesRole(string $name)
    {
        /** @var Role */
        $role = Role::firstOrCreate(['name' => $name]);

        $permissions = Permission::whereIn('name', self::DOS_DEFAULT_PERMISSION)
            ->get(['id'])->pluck('id')->all();

        $role->permissions()->sync($permissions);

        return $role;
    }

    /**
     * Defines the deputy role
     * 
     * @return Role
     */
    private static function createDeputyRole(string $name)
    {
        /** @var Role */
        $role = Role::firstOrCreate(['name' => $name]);

        $permissions = Permission::whereIn('name', self::DEPUTY_DEFAULT_PERMISSION)
            ->get(['id'])->pluck('id')->all();

        $role->permissions()->sync($permissions);

        return $role;
    }
}
