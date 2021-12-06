<div id="generate-level-unit-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="generate-level-unit-modal-title">
    <div class="modal-dialog">
        <form action="{{ route('level-units.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 id="generate-level-unit-modal-title" class="modal-title">Generate Classes Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead">Are you sure you want to generate classes, from the available streams and levels? You can afterwards delete the extra classes that you may not need.</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" wire:click="deleteLevelUnit" class="btn btn-outline-primary">Proceed</button>
            </div>
        </form>
    </div>
</div>