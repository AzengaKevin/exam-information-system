@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<livewire:users :trashed="$trashed" :roleId="$roleId" />

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