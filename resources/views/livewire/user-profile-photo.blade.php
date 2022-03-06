<div class="card shadow-sm h-100">
    <div class="card-header bg-white">
        <h5 class="card-title my-0">Profile Photo</h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-4 text-center d-flex flex-column">
                <img src="{{ $user->fresh()->image() ?? 'https://picsum.photos/200' }}" class="w-100 img-thumbnail rounded-circle"
                    alt="{{ $user->fresh()->name }}">
                <a href="#" data-bs-toggle="modal" data-bs-target="#update-user-profile-photo-modal"
                    class="card-link">Update Photo</a>

            </div>
            <div class="col-md-8">
                <h6 class="h5">{{ $user->fresh()->name }}</h6>
                <span class="text-muted">Joined in {{ optional($user->created_at)->format("Y")}}</span>
                <hr>
            </div>
        </div>
    </div>
    <x-modals.profile.photo :image="$user->fresh()->image()" :user="$user" />
</div>