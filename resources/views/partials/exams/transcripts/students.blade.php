<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>ADMNO</th>
                <th>NAME</th>
                <th>CLASS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @if ($students->count())
            @foreach ($students as $student)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $student->adm_no }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ optional($student->levelUnit)->alias }}</td>
                <td>
                    <div class="hstack gap-2 align-items-center">
                        <a href="{{ route('exams.transcripts.index', [
                                            'exam' => $exam,
                                            'level' => optional($level)->id,
                                            'level-unit' => optional($levelUnit)->id,
                                            'admno' => $student->adm_no
                                        ]) }}" class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                            <i class="fa fa-eye"></i>
                            <span>Transcript</span>
                        </a>
                        <a href="" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                            <i class="fa fa-cog"></i>
                            <span>Transcript</span>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="5">No students taking the exam found</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">
                    {{ $students->links() }}
                    @if ($students->count())
                    <div class="text-muted">{{ $students->firstItem() }} - {{ $students->lastItem() }}
                        out of
                        {{ $students->total() }}</div>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
</div>