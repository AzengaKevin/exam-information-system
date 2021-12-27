<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Adm. No.</th>
                    <th>Name</th>
                    <th>% Score</th>
                    <th>Points</th>
                    <th>Rank</th>
                </tr>
            </thead>
            <tbody>
                @if ($data->count())
                @foreach ($data as $item)
                @php
                $col = $subject->shortname;
                $score = json_decode($item->$col);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->adm_no }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ optional($score)->score }}{{ optional($score)->grade }}</td>
                    <td>{{ optional($score)->points }}</td>
                    <td>{{ optional($score)->rank ?? '-' }} / {{ optional($score)->total ?? '-' }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5">Looks like there are no students, in your
                        <strong>
                            {{ optional($level)->name ?? optional($levelUnit)->alias }} - {{ $subject->name }} Class
                        </strong>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div wire:ignore.self id="generate-rank" class="modal fade" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Subject Ranks</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to generate subject ranks?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                    <button wire:click="rankSubjectResults" type="button" data-bs-dismiss="modal" class="btn btn-outline-primary">Proceed</button>
                </div>
            </div>
        </div>
    </div>
</div>