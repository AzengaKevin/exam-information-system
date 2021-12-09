@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </nav>
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
</script>
@endpush