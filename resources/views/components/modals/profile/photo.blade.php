@props(['image' => null, 'user' => null])

<div wire:ignore.self id="update-user-profile-photo-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="update-user-profile-photo-modal-title">
    <div class="modal-dialog">
        <form wire:submit.prevent="updateUserProfilePhoto" class="modal-content">
            <div class="modal-header">
                <h5 id="update-user-profile-photo-modal-title" class="modal-title">Update Profile Photo</h5>
                <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-5 align-items-center">
                    <div class="col-md-4 text-center d-flex flex-column">
                        <img src="{{ $image ?? 'https://picsum.photos/200' }}"
                            class="w-100 img-thumbnail rounded-circle" alt="{{ $user->name }}">
                    </div>
                    <div class="col-md-8">
                        <div class="">
                            <label for="file" class="form-label">Profile Picture</label>
                            <input type="file" wire:model="file" id="file"
                                class="form-control @error('file') is-invalid @enderror">
                            @error('file')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Nevermind</button>
                <button type="submit" class="btn btn-outline-primary">Update Photo</button>
            </div>
        </form>
    </div>
</div>