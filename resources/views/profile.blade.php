@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row g-3 py-3">
        <div class="col-md-5">
            <h5 class="text-dark">Profile Photo</h5>
            <p class="text-muted">Your profile picture is used within this application, it makes identification much easier for other users.</p>
        </div>
        <div class="col-md-7">
            <livewire:user-profile-photo :user="$user" />
        </div>
        <hr class="my-3 my-md-5">
        <div class="col-md-5">
            <h5 class="text-dark">Profile Details</h5>
            <p class="text-muted">Other than identification, some of the details in this section are essential for notification and instant communications. Make sure the details are verified where applicable.</p>
        </div>
        <div class="col-md-7">
            <livewire:user-profile-information :user="$user" />
        </div>
        <hr class="my-3 my-md-5">
        <div class="col-md-5">
            <h5 class="text-dark">Updated Password</h5>
            <p class="text-muted">Ensure you are using a strong password to stay secure</p>
        </div>
        <div class="col-md-7">
            <livewire:update-user-password :user="$user" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    livewire.on('hide-update-user-profile-information-modal', () => $('#update-user-profile-information-modal').modal(
        'hide'))
    livewire.on('hide-update-user-profile-photo-modal', () => $('#update-user-profile-photo-modal').modal('hide'))
</script>
@endpush