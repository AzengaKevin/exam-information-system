<div class="card h-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="{{ $systemSettings->school_level === 'secondary' ? 6 : 5 }}">{{ $level->name }} {{ $subject->name }} Performance</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Stream</th>
                        @if ($systemSettings->school_level === 'secondary')
                        <th>Points</th>
                        <th>Deviation</th>
                        <th>Grade</th>
                        @else
                        <th>Average</th>
                        <th>Deviation</th>
                        @endif
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($subjects->count())
                    @foreach ($subjects as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $subject->pivot->levelUnit->alias }}</td>
                        @if ($systemSettings->school_level === 'secondary')        
                        <td>{{ $subject->pivot->points }}</td>
                        <td>{{ $subject->pivot->points_deviation ?? '-'  }}</td>
                        <td>{{ $subject->pivot->grade }}</td>
                        @else
                        <td>{{ $subject->pivot->average }}</td>
                        <td>{{ $subject->pivot->average_deviation ?? '-' }}</td>
                        @endif
                        <td>{{ optional($subject->pivot->levelUnit->getSubjectTeacher($subject))->auth->name }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="{{ $systemSettings->school_level === 'secondary' ? 6 : 5 }}">
                            <div class="py-1 text-center">Subject Performance not Published</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>