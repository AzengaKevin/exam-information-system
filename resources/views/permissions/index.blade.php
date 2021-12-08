@extends('layouts.dashboard')

@section('title', 'Permissions')

@section('content')

<div class="d-flex justify-content-between">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Authorization</a></li>
            <li class="breadcrumb-item active" aria-current="page">Permissions</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-permission-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Permission</span>
    </button>
</div>
<hr>

@livewire('permissions')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-permission-modal', () => $('#upsert-permission-modal').modal('show'))
    livewire.on('hide-upsert-permission-modal', () => $('#upsert-permission-modal').modal('hide'))
    livewire.on('show-delete-permission-modal', () => $('#delete-permission-modal').modal('show'))
    livewire.on('hide-delete-permission-modal', () => $('#delete-permission-modal').modal('hide'))

</script>
@endpush