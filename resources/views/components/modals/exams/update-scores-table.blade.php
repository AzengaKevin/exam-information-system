@props(['name'])

<div wire:ignore.self id="update-scores-table-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="update-scores-table-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="update-scores-table-modal-title" class="modal-title">Update Exam Scores Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The operation will add missing columns to the scores table for the current exam,
                    <strong>{{ $name }}</strong>, it will by no means delete any of you previous data, henceit is safe
                    to run.</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" wire:click="updateScoresTable" class="btn btn-outline-info">Update</button>
            </div>
        </div>
    </div>
</div>