<div>
    <div class="card shadow-sm h-100">

    <div class="card-header bg-white">
        <h5 class="card-title my-0">Change Password</h5>
    </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="">
                        <label for="current-password" class="form-label">Current Password</label>
                        <input type="password" wire:model.lazy="current_password" id="current-password"
                            class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <label for="new-password" class="form-label">New Password</label>
                        <input type="password" wire:model.lazy="password" id="new-password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <input type="password" wire:model.lazy="password_confirmation" id="confirm-password" class="form-control">
                    </div>
                    <div class="mt-3"><x-feedback /></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-md-end">
                <button type="button" wire:click="updatePassword" class="btn btn-info">Update Password</button>
            </div>
        </div>
    </div>
</div>