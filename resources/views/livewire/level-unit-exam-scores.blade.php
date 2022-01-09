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
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @if ($data->count())
                        @foreach ($data as $item)
                        <tr>
                            @foreach ($cols as $col)
                            @if (in_array($col, $subjectCols))
                            <td>
                                <span>{{ optional(json_decode($item->$col))->score ?? null }}</span>
                                @if ($systemSettings->school_level == 'secondary')
                                <span>{{ optional(json_decode($item->$col))->grade ?? null }}</span>
                                @endif
                            </td>
                            @else
                            <td>{{ $item->$col }}</td>
                            @endif
                            @endforeach
                            <td>
                                <button wire:click="showGenerateAggregatesModal({{ $item->student_id }})"
                                    class="btn btn-sm btn-outline-primary hstack gap-2">
                                    <i class="fa fa-calculator"></i>
                                    <span>Aggregates</span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="{{ count($cols) + 1 }}">No data found yet</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.exams.scores.level-units.generate-aggregates :name="$name" :levelUnit="$levelUnit" />
    <x-modals.exams.scores.level-units.publish-scores :levelUnit="$levelUnit" />
    <x-modals.exams.scores.level-units.publish-grade-distribution :levelUnit="$levelUnit" />
    <x-modals.exams.scores.level-units.publish-subject-performance :levelUnit="$levelUnit" />
    <x-modals.exams.scores.level-units.rank :columns="$rankCols" />

</div>