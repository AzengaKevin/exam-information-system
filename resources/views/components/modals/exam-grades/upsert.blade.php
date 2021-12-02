@props(['gradeId' => null])

<div wire:ignore.self id="upsert-exam-grades-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-exam-grades-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($gradeId))
                <h5 id="upsert-exam-grades-modal-title" class="modal-title">Add Grade</h5>
                @else
                <h5 id="upsert-exam-grades-modal-title" class="modal-title">Update Grade</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="low" class="form-label">Low</label>
                    <input type="integer" wire:model.lazy="low" id="low"
                        class="form-control @error('low') is-invalid @enderror">
                    @error('low')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="high" class="form-label">High</label>
                    <input type="integer" wire:model.lazy="high" id="high"
                        class="form-control @error('high') is-invalid @enderror">
                    @error('high')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
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
                    <input type="integer" wire:model.lazy="points" id="points"
                        class="form-control @error('points') is-invalid @enderror">
                    @error('points')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($gradeId))
                <button type="submit" wire:click="createGrade" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateGrade" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>