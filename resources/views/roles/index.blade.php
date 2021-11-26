@extends('layouts.dashboard')

@section('title', 'Roles')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Roles</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-role-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Role</span>
    </button>
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