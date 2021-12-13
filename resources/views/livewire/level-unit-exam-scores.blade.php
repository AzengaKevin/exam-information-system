<div>
    <x-feedback />

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
                    <td>{{ optional(json_decode($item->$col))->score ?? null }}{{ optional(json_decode($item->$col))->grade ?? null }}
                    </td>
                    @else
                    <td>{{ $item->$col }}</td>
                    @endif
                    @endforeach
                    <td>
                        <button wire:click="showGenerateAggregatesModal({{ $item->admno }})" class="btn btn-sm btn-outline-primary hstack gap-2">
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

    <x-modals.exams.scores.level-units.generate-aggregates :admno="$admno" :levelUnit="$levelUnit" />
    <x-modals.exams.scores.level-units.publish-scores :levelUnit="$levelUnit" />
    <x-modals.exams.scores.level-units.rank />
    
</div>