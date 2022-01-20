@extends('layouts.dashboard')

@section('title', 'Permissions')

@section('content')

<livewire:permissions :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-permission-modal', () => $('#upsert-permission-modal').modal('show'))
    livewire.on('hide-upsert-permission-modal', () => $('#upsert-permission-modal').modal('hide'))
    livewire.on('show-delete-permission-modal', () => $('#delete-permission-modal').modal('show'))
    livewire.on('hide-delete-permission-modal', () => $('#delete-permission-modal').modal('hide'))

</script>
@endpush