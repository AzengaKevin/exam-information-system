<div wire:ignore.self id="users-bulk-role-update-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="users-bulk-role-update-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="users-bulk-role-update-modal-title" class="modal-title">Update User(s) Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
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
                <button type="submit" wire:click="bulkUsersRoleUpdate" class="btn btn-outline-info">Update</button>
            </div>
        </div>
    </div>
</div>