<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    {{-- <th>#</th> --}}
                    <th>Low</th>
                    <th>High</th>
                    <th>Grade</th>
                    <th>Points</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($grades->count())
                @foreach ($grades as $grade)
                <tr>
                    {{-- <td>{{ $loop->iteration }}</td> --}}
                    <td>{{ $grade->low }}</td>
                    <td>{{ $grade->high }}</td>
                    <td>{{ $grade->grade }}</td>
                    <td>{{ $grade->points }}</td>
                    <td>{{ $grade->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <button wire:click="editGrade({{ $grade }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeleteGradeModal({{ $grade }})" class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5">
                        <div class="py-1">No Grade created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-modals.grades.upsert :gradeId="$gradeId" />
    <x-modals.grades.delete :points="$points" />
    
</div>