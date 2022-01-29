<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Subjects</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex flex-wrap align-items-center gap-2 flex-wrap">
            @can('create', \App\Models\Subject::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-subject-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Subject</span>
            </button>
            @endcan
            @can('bulkDelete', \App\Models\Subject::class)
            <button type="button" data-bs-toggle="modal" data-bs-target="#truncate-subjects-modal"
                class="btn btn-outline-danger d-inline-flex gap-2 align-items-center">
                <i class="fa fa-trash"></i>
                <span>Delete All Subjects</span>
            </button>
            @endcan
            @if (!$trashed)
            @can('viewTrashed', \App\Models\Subject::class)
            <a href="{{ route('subjects.index', ['trashed' => true]) }}"
                class="btn btn-warning d-inline-flex gap-1 align-items-center">
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
                            <th>Department</th>
                            <th>Shortname</th>
                            <th>Teachers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($subjects->count())
                        @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ optional($subject->department)->name ?? 'Not Set' }}</td>
                            <td>{{ $subject->shortname }}</td>
                            <td>{{ $subject->teachers->count() }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $subject)
                                    <button wire:click="showTeachers({{ $subject }})"
                                        class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Teachers</span>
                                    </button>
                                    @endcan
                                    @can('update', $subject)
                                    <button wire:click="editSubject({{ $subject }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $subject)
                                    <button wire:click="showDeleteSubjectModal({{ $subject }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $subject)                                        
                                    <button wire:click="restoreSubject({{ $subject->id }})"
                                        class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $subject)                                        
                                    <button wire:click="destroySubject({{ $subject->id }})"
                                        class="btn btn-sm btn-danger d-inline-flex gap-1 align-items-center">
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
                                <div class="py-1 text-center">No subject {{ $trashed ? 'trashed' : 'created' }} yet
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{ $subjects->links() }}
                                @if ($subjects->count())
                                <div class="text-muted">{{ $subjects->firstItem() }} - {{ $subjects->lastItem() }} out
                                    of
                                    {{ $subjects->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.subjects.upsert 
        :departmentId="$departmentId" 
        :subjectId="$subjectId" 
        :departments="$departments"
        :levels="$levels"
        :segments="$segments" />
    <x-modals.subjects.delete :name="$name" />
    <x-modals.subjects.truncate />

    @include('partials.subjects.teachers')

</div>