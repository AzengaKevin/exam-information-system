@props(['roleId' => null])

<div wire:ignore.self id="upsert-role-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-role-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($roleId))
                <h5 id="upsert-role-modal-title" class="modal-title">Add Role</h5>
                @else
                <h5 id="upsert-role-modal-title" class="modal-title">Update Role</h5>
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
                @if(is_null($roleId))
                <button type="submit" wire:click="createRole" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateRole" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>