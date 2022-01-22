<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Departments</li>
                @endif
            </ol>
        </nav>

        <div class="d-inline-flex gap-2 flex-wrap align-items-center">
            @can('create', \App\Models\Department::class)                
            <button data-bs-toggle="modal" data-bs-target="#upsert-department-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Department</span>
            </button>
            @endcan
            @if (!$trashed)
            @can('viewTrashed', \App\models\Department::class)
            <a href="{{ route('departments.index', ['trashed' => true]) }}" class="btn btn-warning d-inline-flex gap-1 align-items-center">
                <i class="fa fa-eye"></i>
                <span>Trashed</span>
            </a>
            @endcan
            @endif
        </div>
    </div>
    <hr>

    <x-feedback />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Subjects</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($departments->count())
                        @foreach ($departments as $department)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->subjects->count() }}</td>
                            <td>{{ $department->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center justify-content-center">
                                    @if (!$trashed)
                                    @can('view', $department)                                        
                                    <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </button>
                                    @endcan
                                    @can('update', $department)                                        
                                    <button wire:click="editDepartment({{ $department }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $department)                                        
                                    <button wire:click="showDeleteDepartmentModal({{ $department }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan                   
                                    @else
                                    @can('restore', $department)                                        
                                    <button wire:click="restoreDepartment({{ $department->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $department)                                        
                                    <button wire:click="destroyDepartment({{ $department->id }})"
                                        class="btn btn-sm btn-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-alt"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">
                                <div class="py-1">No department {{ $trashed ? "trashed" : "created" }} yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.departments.upsert :departmentId="$departmentId" />
    <x-modals.departments.delete :name="$name" />

</div>