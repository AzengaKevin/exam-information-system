<div wire:ignore.self id="update-user-profile-information-modal" class="modal fade" data-bs-backdrop="static"
    tabindex="-1" aria-labelledby="edit-user-profile-modal-title">
    <div class="modal-dialog">
        <form wire:submit.prevent="updateUserProfileInformation" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-user-profile-modal-title">Update Your Profile</h5>
                <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
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
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" wire:model.lazy="email" id="email"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" wire:model.lazy="phone" id="phone"
                        class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button class="btn btn-outline-primary">Update Profile</button>
            </div>
        </form>
    </div>
</div>