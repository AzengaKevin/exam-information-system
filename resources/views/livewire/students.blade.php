<div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Students</li>
                @endif
            </ol>
        </nav>
        <div class="hstack flex-wrap gap-2">

            @can('create', \App\Models\Student::class)                
            <button data-bs-toggle="modal" data-bs-target="#import-student-spreadsheet-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-file-upload"></i>
                <span>Import</span>
            </button>
            @endcan

            @can('viewAny', \App\Models\Student::class)
            <button data-bs-toggle="modal" data-bs-target="#export-student-spreadsheet-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-file-excel"></i>
                <span>Download</span>
            </button>
            @endcan

            @can('create', \App\Models\Student::class)                
            <button data-bs-toggle="modal" data-bs-target="#upsert-student-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Student</span>
            </button>

            <button data-bs-toggle="modal" data-bs-target="#add-student-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>New Student</span>
            </button>
            @endcan

            @if (!$trashed)
            @can('viewTrashed', \App\Models\Student::class)                
            <a href="{{ route('students.index', ['trashed' => true]) }}"
                class="btn btn-warning d-inline-flex flex-wrap gap-1 align-items-center">
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adm. No</th>
                            @if ($systemSettings->school_level == 'secondary')
                            <th>KCPE</th>
                            @endif
                            <th>Name</th>
                            <th>Class</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($students->count())
                        @foreach ($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->adm_no ?? '-' }}</td>
                            @if ($systemSettings->school_level == 'secondary')
                            <td>{{ $student->kcpe_marks ?? '-' }}</td>
                            @endif
                            <td>{{ $student->name }}</td>
                            @if ($systemSettings->school_has_streams)
                            <td>{{ optional($student->levelUnit)->alias ?? '-' }}</td>
                            @else
                            <td>{{ optional($student->level)->name ?? '-' }}</td>
                            @endif
                            <td>{{ optional($student->dob)->diffInYears(now()) ?? '-' }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $student)
                                    <a href="{{route('students.show', $student)}}"
                                        class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                    <a href="{{ route('students.subjects.index', $student) }}" class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Subjects</span>
                                    </a>
                                    @endcan
                                    @can('update', $student)
                                    <button wire:click="showAddStudentGuardiansModal({{ $student }})"
                                        class="btn btn-sm btn-outline-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-plus"></i>
                                        <span>Guardians</span>
                                    </button>
                                    <button wire:click="editStudent({{ $student }})"
                                        class="btn btn-sm btn-outline-info d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>

                                    <button wire:click="archiveStudent({{ $student }})"
                                        class="btn btn-sm btn-outline-warning d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-archive"></i>
                                        <span>Archive</span>
                                    </button>
                                    @endcan
                                    @can('delete', $student)
                                    <button wire:click="showDeleteStudentModal({{ $student }})"
                                        class="btn btn-sm btn-outline-danger d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $student)
                                    <button wire:click="restoreStudent({{ $student->id }})"
                                        class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $student)
                                    <button wire:click="destroyStudent({{ $student->id }})"
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
                            <td colspan="{{ $systemSettings->school_level == 'secondary' ? '7' : '6' }}">
                                <div class="py-1 text-center">No student {{ !$trashed ? "added" : "trashed" }} yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="{{ $systemSettings->school_level == 'secondary' ? '7' : '6' }}">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                    {{ $students->links() }}
                                    @if ($students->count())
                                    <div class="text-muted ms-md-3">{{ $students->firstItem() }} -
                                        {{ $students->lastItem() }}
                                        out of
                                        {{ $students->total() }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.students.upsert :studentId="$studentId" :streams="$streams" :levels="$levels" :hostels="$hostels"
        :genderOptions="$genderOptions" :kcpeGradeOptions="$kcpeGradeOptions" :systemSettings="$systemSettings" />

    <x-modals.students.add :studentId="$studentId" :streams="$streams" :levels="$levels" :hostels="$hostels"
        :genderOptions="$genderOptions" :kcpeGradeOptions="$kcpeGradeOptions" :systemSettings="$systemSettings" />

    <x-modals.students.delete :name="$name" />

    <x-modals.students.export-spreadsheet />
    <x-modals.students.import-spreadsheet />

</div>