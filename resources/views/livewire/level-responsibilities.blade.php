<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="5">{{ $level->name }} Responsibilities</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Responsibility</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($responsibilities->count())
                    @foreach ($responsibilities as $responsibility)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $responsibility->name }}</td>
                        <td>{{ $responsibility->pivot->subject->name }}</td>
                        <td>{{ $responsibility->pivot->teacher->auth->name }}</td>
                        <td>
                            <a href="{{ route('teachers.show', $responsibility->pivot->teacher) }}" class="btn btn-sm btn-outline-primary d-inline-flex gap-2 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Teacher</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="4">
                            <div class="py-1 text-center">No Responsibility assigned yet</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>