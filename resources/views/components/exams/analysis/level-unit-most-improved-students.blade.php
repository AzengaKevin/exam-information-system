<div class="card h-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="5">{{ $levelUnit->alias }} Most Improved Students</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>ADM</th>

                        @if ($systemSettings->school_level === 'secondary')
                        <th>TP</th>
                        <th>Dev</th>
                        @else
                        <th>TM</th>
                        <th>Dev</th>
                        @endif
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
                        <td>{{ $student->pivot->tp }}</td>
                        <td>{{ $student->pivot->tpd ?? 0 }}</td>
                        @else
                        <td>{{ $student->pivot->tm }}</td>
                        <td>{{ $student->pivot->tmd ?? 0 }}</td>
                        @endif
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5">
                            <div class="py-1 text-center">No student Improved</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>