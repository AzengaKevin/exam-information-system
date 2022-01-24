@props(['name', 'permissions' => []])

<div wire:ignore.self id="update-permissions-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="update-permissions-modal-title">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="updatePermissions" class="modal-content">
            <div class="modal-header">
                <h5 id="update-permissions-modal-title" class="modal-title">Update {{ $name }} Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
                        @foreach ($permissions as $permission)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedPermissions.{{ $permission->id }}" id="permission-{{ $loop->iteration }}" class="form-check-control" value="true">
                                <label for="permission-{{ $loop->iteration }}" class="form-check-label">{{ $permission->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-outline-info">Update</button>
            </div>
        </form>
    </div>
</div>