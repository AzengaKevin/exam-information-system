<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Teachers</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex flex-wrap gap-2 align-items-center">
            @can('create', \App\Models\Teacher::class)                
            <button data-bs-toggle="modal" data-bs-target="#upsert-teacher-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Teacher</span>
            </button>
            @endcan
            @if (!$trashed)
                @can('viewTrashed', \App\Models\Teacher::class)
                    <a href="{{ route('teachers.index', ['trashed' => true]) }}" class="btn btn-warning d-inline-flex gap-1 align-items-center">
                        <i class="fa fa-eye"></i>
                        <span>Trash</span>
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Tasks</th>
                            <th>Employer</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($teachers->count())
                        @foreach ($teachers as $teacher)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($teacher->auth)->name ?? '-' }}</td>
                            <td>{{ optional($teacher->auth)->phone ?? '-' }}</td>
                            <td>{{ $teacher->responsibilities->count() }}</td>
                            <td>{{ $teacher->employer ?? '-' }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $teacher)
                                    <a href="{{ route('teachers.show', $teacher) }}"
                                        class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                    @endcan
                                    @can('manageTeacherResponsibilities', $teacher)
                                    <a href="{{ route('teachers.responsibilities.index', $teacher) }}"
                                        class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Tasks</span>
                                    </a>
                                    @endcan

                                    @can('update', $teacher)
                                    <button wire:click="editTeacher({{ $teacher }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan

                                    @can('delete', $teacher)
                                    <button wire:click="showDeleteTeacherModal({{ $teacher }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-alt"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $teacher)                                        
                                    <button wire:click="restoreTeacher({{ $teacher->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $teacher)                                     
                                    <button wire:click="destroyTeacher({{ $teacher->id }})"
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
                            <td colspan="6">
                                <div class="py-1 text-center">No Teachers Added Yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{ $teachers->links() }}
                                @if ($teachers->count())
                                <div class="text-muted">{{ $teachers->firstItem() }} - {{ $teachers->lastItem() }} out
                                    of
                                    {{ $teachers->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.teachers.upsert :teacherId="$teacherId" :employers="$employers" :subjects="$subjects" />
    <x-modals.teachers.delete :name="$name" />

</div>