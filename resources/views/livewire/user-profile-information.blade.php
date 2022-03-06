<div class="card shadow-sm h-100">
    <div class="card-header bg-white">
        <h5 class="card-title my-0">Profile Information</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <x-feedback />
            </div>
            <div class="col-md-6">
                <dl>
                    <dt>Name</dt>
                    <dd>{{ $user->fresh()->name }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl>
                    <dt>Email</dt>
                    <dd>{{ $user->fresh()->email }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl>
                    <dt>Phone</dt>
                    <dd>{{ $user->fresh()->phone }}</dd>
                </dl>
            </div>
            <div class="col-md-12 d-flex justify-content-center justify-content-sm-end">
                <button data-bs-toggle="modal" data-bs-target="#update-user-profile-information-modal"
                    class="btn btn-outline-primary">Edit Profile</button>
            </div>
        </div>
    </div>

    <x-modals.profile.update />
</div>