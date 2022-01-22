@extends('layouts.dashboard')

@section('title', 'Departments')

@section('content')

<livewire:departments :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-department-modal', () => $('#upsert-department-modal').modal('show'))
    livewire.on('hide-upsert-department-modal', () => $('#upsert-department-modal').modal('hide'))
    livewire.on('show-delete-department-modal', () => $('#delete-department-modal').modal('show'))
    livewire.on('hide-delete-department-modal', () => $('#delete-department-modal').modal('hide'))

</script>
@endpush