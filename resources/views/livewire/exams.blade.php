<div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Exams</li>
                @endif
            </ol>
        </nav>

        <div class="d-inline-flex flex-wrap gap-2 align-items-md-center">
            @can('create', \App\Models\Exam::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-exam-modal"
                class="d-md-none btn btn-outline-primary rounded-circle">
                <i class="fa fa-plus"></i>
            </button>
            <button data-bs-toggle="modal" data-bs-target="#upsert-exam-modal"
                class="d-none d-md-inline-flex btn btn-outline-primary gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Exam</span>
            </button>
            @endcan

            @if (!$trashed)
            @can('viewTrashed', \App\Models\Exam::class)
            <a href="{{ route('exams.index', ['trashed' => true]) }}"
                class="btn btn-warning d-inline-flex gap-1 align-items-center">
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
                            <th>Name</th>
                            <th>Year</th>
                            <th>Term</th>
                            @if (false)
                            <th>Weight</th>
                            <th>Counts</th>
                            @endif
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($exams->count())
                        @foreach ($exams as $exam)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $exam->name }}</td>
                            <td>{{ $exam->year }}</td>
                            <td>{{ $exam->term }}</td>
                            @if (false)
                            <td>{{ $exam->weight }}</td>
                            <td>{{ $exam->counts ? 'True' : 'False' }}</td>
                            @endif
                            <td>{{ $exam->status }}</td>
                            <td>
                                <div class="d-inline-flex gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $exam)
                                    <a href="{{ route('exams.show', $exam) }}"
                                        class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                    @endcan
                                    @can('update', $exam)
                                    <button wire:click="editExam({{ $exam }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button wire:click="showEnrollLevelsModal({{ $exam }})"
                                        class="btn btn-sm btn-outline-secondary hstack gap-1 align-items-center">
                                        <i class="fa fa-cog"></i>
                                        <span>Levels</span>
                                    </button>
                                    <button wire:click="showEnrollSubjectsModal({{ $exam }})"
                                        class="btn btn-sm btn-outline-success hstack gap-1 align-items-center">
                                        <i class="fa fa-cog"></i>
                                        <span>Subjects</span>
                                    </button>
                                    @endcan
                                    @can('delete', $exam)
                                    <button wire:click="showDeleteExamModal({{ $exam }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $exam)
                                    <button wire:click="restoreExam({{ $exam->id }})"
                                        class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $exam)
                                    <button wire:click="destroyExam({{ $exam->id }})"
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
                        <tr class="text-center">
                            <td colspan="6">
                                <div class="py-1">No exam has been {{ $trashed ? "trashed" : "created" }} yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{ $exams->links() }}
                                @if ($exams->count())
                                <div class="text-muted">{{ $exams->firstItem() }} - {{ $exams->lastItem() }} out of
                                    {{ $exams->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.exams.upsert :examId="$examId" :terms="$terms" :examStatusOptions="$examStatusOptions" :levels="$levels"
        :subjects="$subjects" :otherExams="$otherExams" />
    <x-modals.exams.delete :name="$name" />
    <x-modals.exams.enroll-levels :shortname="$shortname" :levels="$levels" />
    <x-modals.exams.enroll-subjects :shortname="$shortname" :subjects="$subjects" />

</div>