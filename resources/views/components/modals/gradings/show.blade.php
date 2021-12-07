@props(['name' => null, 'values' => []])

<div wire:ignore.self id="grading-instance-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="grading-instance-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="grading-instance-modal-title" class="modal-title">{{ $name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Grade</th>
                            <th>Points</th>
                        </thead>
                        <tbody>
                            @foreach ($values as $value)
                            <tr>
                                <td>{{ $value['min'] }}</td>
                                <td>{{ $value['max'] }}</td>
                                <td>{{ $value['grade'] }}</td>
                                <td>{{ $value['points'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>