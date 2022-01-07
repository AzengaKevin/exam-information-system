<div class="card h-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="{{ $colsCount }}">{{ $levelUnit->alias }} Top Students</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>ADM</th>

                        @if ($systemSettings->school_level === 'secondary')
                        <th>MP</th>
                        <th>TP</th>
                        <th>MG</th>
                        @else
                        <th>MM</th>
                        <th>TM</th>
                        @endif

                        @if ($systemSettings->school_has_streams)
                        <th>SP</th>
                        @endif
                        <th>OP</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($students->count())
                    @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->adm_no }}</td>
                        @if ($systemSettings->school_level === 'secondary')
                        <td>{{ $student->pivot->mp }}</td>
                        <td>{{ $student->pivot->tp }}</td>
                        <td>{{ $student->pivot->mg }}</td>
                        @else
                        <td>{{ $student->pivot->mm }}</td>
                        <td>{{ $student->pivot->tm }}</td>
                        @endif

                        @if ($systemSettings->school_has_streams)
                        <td>{{ $student->pivot->sp }}</td>
                        @endif
                        <td>{{ $student->pivot->op }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="{{ $colsCount }}">
                            <div class="py-1 text-center">Student Performance not Published</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>