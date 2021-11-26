@props(['roles' => []])

<div wire:ignore.self id="update-user-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="update-user-modal-title">
    <div class="modal-dialog">
        <form wire:submit.prevent="updateUser" class="modal-content">
            <div class="modal-header">
                <h5 id="update-user-modal-title" class="modal-title">Update User</h5>
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
                <div class="mt-3">
                    <label for="role" class="form-label">Role</label>
                    <select wire:model="role_id" id="role" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="">-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-outline-info">Update</button>
            </div>
        </form>
    </div>
</div>