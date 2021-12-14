@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </nav>

    <div class="d-inline-flex gap-2">
        <button class="btn btn-outline-primary d-inline-fle gap-2" data-bs-toggle="modal" data-bs-target="#users-bulk-role-update-modal">
            <i class="fa fa-pencil-alt"></i>
            <span>Role</span>
        </button>
    </div>
</div>
<hr>

<livewire:users />

@endsection

@push('scripts')
<script>
    livewire.on('show-update-user-modal', () => $('#update-user-modal').modal('show'))
    livewire.on('hide-update-user-modal', () => $('#update-user-modal').modal('hide'))

    livewire.on('show-delete-user-modal', () => $('#delete-user-modal').modal('show'))
    livewire.on('hide-delete-user-modal', () => $('#delete-user-modal').modal('hide'))

    livewire.on('hide-users-bulk-role-update-modal', () => $('#users-bulk-role-update-modal').modal('hide'))
</script>
@endpush