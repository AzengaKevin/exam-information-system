<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Active?</th>
                    <th>Employer</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($teachers->count())
                @foreach ($teachers as $teacher)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($teacher->auth)->name }}</td>
                    <td>{{ optional($teacher->auth)->email }}</td>
                    <td>{{ optional($teacher->auth)->active ? 'True' : 'False' }}</td>
                    <td>{{ $teacher->employer }}</td>
                    <td>{{ $teacher->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editTeacher({{ $teacher }})"
                                class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                <i class="fa fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7">
                        <div class="py-1 text-center">No Teachers Added Yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        {{ $teachers->links() }}
                        @if ($teachers->count())
                        <div class="text-muted">{{ $teachers->firstItem() }} - {{ $teachers->lastItem() }} out of
                            {{ $teachers->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <x-modals.teachers.upsert :teacherId="$teacherId" :employers="$employers" />

</div>