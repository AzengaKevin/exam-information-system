@extends('layouts.dashboard')

@section('title', 'Roles')

@section('content')

<livewire:roles :trashed="$trashed" />

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