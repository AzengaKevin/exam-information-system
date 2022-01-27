@props(['permissionId' => null])

<div wire:ignore.self id="upsert-permission-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-permission-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($permissionId))
                <h5 id="upsert-permission-modal-title" class="modal-title">Add Permission</h5>
                @else
                <h5 id="upsert-permission-modal-title" class="modal-title">Update Permission</h5>
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
                    <label for="description" class="form-label">Description</label>
                    <textarea wire:model.lazy="description" id="description" cols="100" rows="3"
                        class="form-control @error('description') is-invalid @enderror"></textarea>
                    @error('description')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($permissionId))
                <button type="submit" wire:click="createPermission" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updatePermission" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>