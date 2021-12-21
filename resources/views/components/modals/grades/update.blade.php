@props(['grade' => null])

<div wire:ignore.self id="update-grade-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="update-grade-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="update-grade-modal-title" class="modal-title">Update Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-3">
                    <label for="points" class="form-label">Points</label>
                    <input type="number" wire:model.lazy="points" id="points" min="0" max="12"
                        class="form-control @error('points') is-invalid @enderror">
                    @error('points')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="english-comment" class="form-label">English Comment</label>
                    <textarea wire:model.lazy="english_comment" id="english-comment" cols="100" rows="3"
                        class="form-control @error('english_comment') is-invalid @enderror"></textarea>
                    @error('english_comment')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="swahili-comment" class="form-label">Swahili Comments</label>
                    <textarea wire:model.lazy="swahili_comment" id="swahili-comment" cols="100" rows="3"
                        class="form-control @error('swahili_comment') is-invalid @enderror"></textarea>
                    @error('swahili_comment')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" wire:click="updateGrade" class="btn btn-outline-info">Update</button>
            </div>
        </div>
    </div>
</div>