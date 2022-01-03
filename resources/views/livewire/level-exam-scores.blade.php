<div>
    <x-feedback />

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="text-uppercase">
                        @foreach ($cols as $col)
                        <th>{{ $col }}</th>
                        @endforeach
                    </thead>
                    <tbody>
                        @if ($data->count())
                        @foreach ($data as $item)
                        <tr>
                            @foreach ($cols as $col)
                            @if (in_array($col, $subjectCols))
                            @php $score = json_decode($item->$col); @endphp
                            <td>
                                <span>{{ optional($score)->score ?? null }}</span>
                                @if ($systemSettings->school_level == 'secondary')
                                <span>{{ optional($score)->grade ?? null }}</span>
                                @endif
                            </td>
                            @else
                            <td>{{ $item->$col }}</td>
                            @endif
                            @endforeach
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="{{ count($cols) }}" class="text-center">No data found yet</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.exams.scores.levels.generate-aggregates :level="$level" />
    <x-modals.exams.scores.levels.publish-scores :level="$level" />
    <x-modals.exams.scores.levels.rank :columns="$columns" />
    <x-modals.exams.scores.levels.publish-grade-distribution :level="$level" />
    <x-modals.exams.scores.levels.publish-subject-performance :level="$level" />
    <x-modals.exams.scores.levels.publish-students-results :level="$level" />

</div>