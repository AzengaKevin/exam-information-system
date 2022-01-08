@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row g-3 py-3">
        <div class="col-md-6">
            <livewire:user-profile-photo :user="$user" />
        </div>
        <div class="col-md-6">
            <livewire:user-profile-information :user="$user" />
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