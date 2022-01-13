<div>
    <!-- It is not the man who has too little, but the man who craves more, that is poor. - Seneca -->
    @foreach ($groupedLevelUnitWithScores as $levelName => $levelUnitWithScores)
        
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th colspan="4">{{ $levelName }} Streams</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @if ($levelUnitWithScores->count())
                @foreach ($levelUnitWithScores as $levelUnit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $levelUnit->alias }}</td>
                    <td>
                        @if ($levelUnit->average)
                        <span class="badge py-2 fs-6 bg-success">Published</span>
                        @else
                        <span class="badge py-2 fs-6 bg-warning">Not Published</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-inline-flex gap-2 align-items-center">
                            <a href="{{ route('exams.scores.manage', [
                                'exam' => $exam,
                                'level-unit' => $levelUnit->id
                            ]) }}"
                                class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                <i class="fa fa-cog"></i>
                                <span>Manage Stream</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                @endif
            </tbody>
        </table>
    </div>
    @endforeach
</div>