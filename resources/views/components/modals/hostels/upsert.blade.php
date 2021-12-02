@props(['hostelId' => null])

<div wire:ignore.self id="upsert-hostel-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-hostel-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($hostelId))
                <h5 id="upsert-hostel-modal-title" class="modal-title">Add Hostel</h5>
                @else
                <h5 id="upsert-hostel-modal-title" class="modal-title">Update Hostel</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" wire:model.lazy="name" id="name"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="name" class="form-label">Description</label>
                    <input type="text" wire:model.lazy="description" id="description"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('description')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($hostelId))
                <button type="submit" wire:click="createHostel" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateHostel" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>