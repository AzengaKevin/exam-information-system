@props(['level'])

<div wire:ignore.self id="publish-class-scores-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="publish-class-scores-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="publish-class-scores-modal-title" class="modal-title">Publish {{ $level->name }} Scores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure want to publish level scores for <strong>{{ $level->name }}</strong>? You can re-publish scores until the exam is published</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="publishLevelScores" class="btn btn-outline-primary">Proceed</button>
            </div>
        </div>
    </div>
</div>