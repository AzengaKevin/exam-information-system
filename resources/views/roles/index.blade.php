@extends('layouts.dashboard')

@section('title', 'Roles')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Roles</li>
        </ol>
    </nav>
    <div class="hstack gap-2">
        @can('viewAny', \App\Models\Permission::class)
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary  hstack gap-2 align-items-center">
            <i class="fa fa-users-cog"></i>
            <span>Permissions</span>
        </a>
        @endcan
        <button data-bs-toggle="modal" data-bs-target="#upsert-role-modal"
            class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Role</span>
        </button>
    </div>
</div>
<hr>

@livewire('roles')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-role-modal', () => $('#upsert-role-modal').modal('show'))
    livewire.on('hide-upsert-role-modal', () => $('#upsert-role-modal').modal('hide'))

    livewire.on('show-delete-role-modal', () => $('#delete-role-modal').modal('show'))
    livewire.on('hide-delete-role-modal', () => $('#delete-role-modal').modal('hide'))

    livewire.on('show-update-permissions-modal', () => $('#update-permissions-modal').modal('show'))
    livewire.on('hide-update-permissions-modal', () => $('#update-permissions-modal').modal('hide'))
</script>
@endpush