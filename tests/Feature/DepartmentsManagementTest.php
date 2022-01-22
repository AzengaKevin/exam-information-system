<?php

namespace Tests\Feature;

use App\Http\Livewire\Departments;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class DepartmentsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $this->role = Role::factory()->create()
        ]);

        $this->actingAs($user);
    }


    /** @group departments */
    public function testAuthorizedUserCanVisitDepartmentsPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Departments Browse']));

        Department::factory(2)->create();

        $response = $this->get(route('departments.index'));

        $response->assertOk();

        $response->assertViewIs('departments.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('departments');
        
    }

    /** @group departments */
    public function testAuthorizedUserCanCreateADepartment()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Departments Create']));

        $payaload = Department::factory()->make()->toArray();

        Livewire::test(Departments::class)
            ->set('name', $payaload['name'])
            ->set('description', $payaload['description'])
            ->call('createDepartment');

        $department = Department::first();

        $this->assertNotNull($department);

        $this->assertEquals($payaload['name'], $department->name);

        $this->assertEquals($payaload['description'], $department->description);
        
    }

    /** @group departments */
    public function testAuthorizedUserCanUpdateADepartment()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Departments Update']));

        /** @var Department */
        $department = Department::factory()->create();

        $payaload = Department::factory()->make()->toArray();

        Livewire::test(Departments::class)
            ->call('editDepartment', $department)
            ->set('name', $payaload['name'])
            ->set('description', $payaload['description'])
            ->call('updateDepartment');

        $this->assertEquals($payaload['name'], $department->fresh()->name);

        $this->assertEquals($payaload['description'], $department->fresh()->description);
        
    }

    /** @group departments */
    public function testAuthorizedUserCanDeleteADeparment()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Departments Delete']));

        /** @var Department */
        $department = Department::factory()->create();

        Livewire::test(Departments::class)
            ->call('showDeleteDepartmentModal', $department)
            ->call('deleteDepartment');

        $this->assertSoftDeleted($department);
        
    }

    /** @group department */
    public function testAuthorizedUserCanRestoreATrashedDepartment()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Departments Restore']));

        /** @var Department */
        $department = Department::factory()->create();

        $department->delete();

        $this->assertSoftDeleted($department);

        Livewire::test(Departments::class)->call('restoreDepartment', $department->id);

        $this->assertFalse($department->fresh()->trashed());
    }

    /** @group department */
    public function testAuthorizedUserCanDestroyATrashedDepartment()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Departments Destroy']));

        /** @var Department */
        $department = Department::factory()->create();

        $department->delete();

        $this->assertSoftDeleted($department);

        Livewire::test(Departments::class)->call('destroyDepartment', $department->id);

        $this->assertTrue(Department::where('id', $department->id)->withTrashed()->doesntExist());
    }
    
}
