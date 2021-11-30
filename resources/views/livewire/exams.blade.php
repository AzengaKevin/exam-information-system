<div>
    
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Shortname</th>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Weight</th>
                    <th>Counts</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($exams->count())
                @foreach ($exams as $exam)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exam->shortname }}</td>
                    <td>{{ $exam->year }}</td>
                    <td>{{ $exam->term }}</td>
                    <td>{{ $exam->start_date }}</td>
                    <td>{{ $exam->end_date }}</td>
                    <td>{{ $exam->weight }}</td>
                    <td>{{ $exam->counts ? 'True' : 'False' }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editExam({{ $exam }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showEnrollLevelsModal({{ $exam }})" class="btn btn-sm btn-outline-secondary hstack gap-1 align-items-center">
                                <i class="fa fa-check"></i>
                                <span>Levels</span>
                            </button>
                            <button wire:click="showDeleteExamModal({{ $exam }})" class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="10">
                        <div class="py-1">No Exam created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
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

    <x-modals.exams.upsert :examId="$examId" :terms="$terms"/>
    <x-modals.exams.delete :name="$name" />
    <x-modals.exams.enroll-levels :shortname="$shortname" :levels="$levels" />
    
</div>