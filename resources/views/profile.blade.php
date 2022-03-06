@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <div class="row g-3">

        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-md-0">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Welcome</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </nav>
        </div>

        <div class="col-md-6">
            <livewire:user-profile-photo :user="$user" />
        </div>

        <div class="col-md-6">
            <livewire:user-profile-information :user="$user" />
        </div>
        <div class="col-md-6">
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