@props(['userId' => null])

<div wire:ignore.self id="upsert-user-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-user-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($userId))
                <h5 id="upsert-user-modal-title" class="modal-title">Add User</h5>
                @else
                <h5 id="upsert-user-modal-title" class="modal-title">Update User</h5>
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
                    <label for="email" class="form-label">Email</label>
                    <input type="email" wire:model.lazy="email" id="email"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <div class="form-check">
                        <input type="checkbox" wire:model="active" id="active" class="form-check-input" value="true">
                        <label for="active" class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if (is_null($userId))
                <button type="button" class="btn btn-outline-primary">Submit</button>
                @else
                <button type="button" wire:click="updateUser" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>