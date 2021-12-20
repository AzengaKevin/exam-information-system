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
                <div class="">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="text" wire:model.lazy="grade" id="grade"
                        class="form-control @error('grade') is-invalid @enderror">
                    @error('grade')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
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
                    <label for="english-comments" class="form-label">English Comments</label>
                    <textarea wire:model.lazy="english_comments" id="english-comments" cols="100" rows="3"
                        class="form-control @error('english_comments') is-invalid @enderror"></textarea>
                    @error('english_comments')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="swahili-comments" class="form-label">Swahili Comments</label>
                    <textarea wire:model.lazy="swahili_comments" id="swahili-comments" cols="100" rows="3"
                        class="form-control @error('swahili_comments') is-invalid @enderror"></textarea>
                    @error('swahili_comments')
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