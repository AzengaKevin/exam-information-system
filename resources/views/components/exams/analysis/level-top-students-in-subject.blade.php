<div class="card h-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="3">{{ $level->name }} Top {{ $subject->name }} Students</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($students->count())
                    @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->pivot->score }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="3">
                            <div class="py-1 text-center">{{ $level->name }} Student Subject Performance not Published</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>