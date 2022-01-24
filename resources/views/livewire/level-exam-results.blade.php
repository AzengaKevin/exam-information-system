<div>
    <x-feedback />

    <div class="d-inline-flex flex-wrap gap-2">
        <button data-bs-toggle="modal" data-bs-target="#filter-level-{{ $level->id }}-exam-results-modal"
            class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
            <i class="fa fa-filter"></i>
            <span>Filter Results</span>
        </button>
        <button data-bs-toggle="modal" data-bs-target="#order-level-{{ $level->id }}-exam-results-modal"
            class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
            <i class="fa fa-sort"></i>
            <span>Order Results</span>
        </button>

        @if ($systemSettings->school_has_streams)           
        <a href="{{ route('exams.results.index', [
            'exam' => $exam,
            'level' => $level->id
        ]) }}" class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
            <i class="fa fa-eye"></i>
            <span>Details</span>
        </a>
        @endif
        <a href="{{ route('exams.merit-list.download', [
            'exam' => $exam,
            'level' => $level->id
        ]) }}" class="btn btn-outline-primary d-inline-flex gap-2 align-items-center" download>
            <i class="fa fa-print"></i>
            <span>Print List</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="text-uppercase">
                <tr class="text-center">
                    <th colspan="{{ count($cols) }}">{{ $level->name }} - {{ $exam->name }} Results</th>
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
                    <td>{{ $item->$col ?? '-' }}</td>
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

    @include('partials.exams.results.levels.modal.filter')

    @include('partials.exams.results.levels.modal.order')

</div>