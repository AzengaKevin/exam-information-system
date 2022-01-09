@props(['name' => null, 'levelUnit'])

<div wire:ignore.self id="generate-scores-aggregates-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="generate-scores-aggregates-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="generate-scores-aggregates-modal-title" class="modal-title">Generate {{ $name }} Aggregates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure want to generate aggregates for <strong>@if (is_null($name)) {{ $levelUnit->alias }}
                        @else {{ $name }}@endif</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if (is_null($name))
                <button type="button" wire:click="generateBulkAggregates" class="btn btn-outline-primary">Proceed</button>
                @else
                <button type="button" wire:click="generateAggregates" class="btn btn-outline-primary">Proceed</button>
                @endif
            </div>
        </div>
    </div>
</div>