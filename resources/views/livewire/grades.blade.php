<div>
    <x-feedback />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Points</th>
                            <th>English Comment</th>
                            <th>Swahili Comment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($grades->count())
                        @foreach ($grades as $grade)
                        <tr>
                            <td>{{ $grade->grade }}</td>
                            <td>{{ $grade->points }}</td>
                            <td>{{ $grade->english_comment }}</td>
                            <td>{{ $grade->swahili_comment }}</td>
                            <td>
                                @can('update', $grade)
                                <button wire:click="editGrade({{ $grade }})"
                                    class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                    <i class="fa fa-edit"></i>
                                    <span>Edit</span>
                                </button>
                                @endcan
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
        </div>
    </div>

    <x-modals.grades.update :grade="$grade" />

</div>