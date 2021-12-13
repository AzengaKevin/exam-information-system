<div>
    <x-feedback />

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
                    <td>{{ optional(json_decode($item->$col))->score ?? null }}{{ optional(json_decode($item->$col))->grade ?? null }}
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

    <x-modals.exams.scores.levels.publish-scores :level="$level" />
    <x-modals.exams.scores.levels.rank :columns="$columns" />
    <x-modals.exams.scores.levels.publish-grade-distribution :level="$level" />
    <x-modals.exams.scores.levels.publish-subject-performance :level="$level" />
    <x-modals.exams.scores.levels.publish-subject-performance :level="$level" />

</div>