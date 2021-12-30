@props(['guardianId' => null])

<div wire:ignore.self id="upsert-guardian-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-guardian-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($guardianId))
                <h5 id="upsert-guardian-modal-title" class="modal-title">Add Guardian</h5>
                @else
                <h5 id="upsert-guardian-modal-ti tle" class="modal-title">Update Guardian</h5>
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
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" wire:model.lazy="phone" id="phone" aria-describedby="phone-help"
                        class="form-control @error('phone') is-invalid @enderror" placeholder="254707427854">
                    <div id="phone-help" class="form-text">Begin with the Kenyas country code(254) without the (+) symbol.</div>
                    @error('phone')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="profession" class="form-label">Profession</label>
                    <input type="text" wire:model.lazy="profession" id="profession"
                        class="form-control @error('profession') is-invalid @enderror">
                    @error('profession')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" wire:model.lazy="location" id="location"
                        class="form-control @error('location') is-invalid @enderror">
                    @error('location')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>

                @if (is_null($guardianId))
                <button type="button" wire:click="addGuardian" class="btn btn-outline-primary">Submit</button>
                @else
                <button type="button" wire:click="updateGuardian" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>