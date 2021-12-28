<div wire:ignore.self id="truncate-levels-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="truncate-levels-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="truncate-levels-modal-title" class="modal-title">Delete All levels Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead">Are you sure you want to delete all the levels in the system, the action is irreversable you might to save the data somewhere else first</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" wire:click="truncateLevels" class="btn btn-outline-danger">Confirm</button>
            </div>
        </div>
    </div>
</div>