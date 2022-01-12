<div>

    <x-feedback />

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
                                <i class="fa fa-check"></i>
                                <span>Levels</span>
                            </button>
                            <button wire:click="showEnrollSubjectsModal({{ $exam }})"
                                class="btn btn-sm btn-outline-success hstack gap-1 align-items-center">
                                <i class="fa fa-check"></i>
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
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr class="text-center">
                    <td colspan="6">
                        <div class="py-1">No Exam created yet</div>
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

    <x-modals.exams.upsert :examId="$examId" :terms="$terms" :examStatusOptions="$examStatusOptions" :levels="$levels"
        :subjects="$subjects" />
    <x-modals.exams.delete :name="$name" />
    <x-modals.exams.enroll-levels :shortname="$shortname" :levels="$levels" />
    <x-modals.exams.enroll-subjects :shortname="$shortname" :subjects="$subjects" />

</div>