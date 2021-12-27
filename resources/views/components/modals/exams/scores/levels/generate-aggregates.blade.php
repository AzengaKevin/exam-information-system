@props(['level'])

<div wire:ignore.self id="generate-aggregates-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="generate-aggregates-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="generate-aggregates-modal-title" class="modal-title">Generate {{ $level->name }} Aggregates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure want to generate aggregates for <strong>{{ $level->name }}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="generateBulkLevelAggregates" class="btn btn-outline-primary">Proceed</button>
            </div>
        </div>
    </div>
</div>