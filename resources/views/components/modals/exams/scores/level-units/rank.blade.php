@props(['admno' => null, 'levelUnit'])

<div wire:ignore.self id="rank-class-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="rank-class-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="rank-class-modal-title" class="modal-title">Generate Ranks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This will rank students based on the selected criteria, defaulting to stdents totak score. Only rank
                    if student eggregates have been generate, rnking depends on the aggregates.</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="generateRanks" class="btn btn-outline-primary">Proceed</button>
            </div>
        </div>
    </div>
</div>