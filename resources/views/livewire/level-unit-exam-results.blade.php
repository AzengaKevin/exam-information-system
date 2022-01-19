<div>
    <x-feedback />

    <div class="d-inline-flex flex-wrap gap-2">
        <a href="{{ route('exams.merit-list.download', [
            'exam' => $exam,
            'level-unit' => $levelUnit->id
        ]) }}" class="btn btn-outline-primary d-inline-flex gap-2 align-items-center" download>
            <i class="fa fa-print"></i>
            <span>Print List</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="text-uppercase">
                <tr class="text-center">
                    <th colspan="{{ count($cols) }}">{{ $levelUnit->alias }} - {{ $exam->name }} Results</th>
                </tr>
                <tr>
                    @foreach ($cols as $col)
                    <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if ($data->count())
                @foreach ($data as $item)
                <tr>
                    @foreach ($cols as $col)
                    @if (in_array($col, $subjectCols))
                    @php
                    $score = json_decode($item->$col);
                    @endphp
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
            <tfoot>
                <tr>
                    <td colspan="{{ count($cols) }}">
                        {{ $data->links() }}
                        @if ($data->count())
                        <div class="text-muted">{{ $data->firstItem() }} - {{ $data->lastItem() }} out of
                            {{ $data->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>