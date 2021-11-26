@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Users</h1>
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