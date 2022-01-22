@extends('layouts.dashboard')

@section('title', 'Students')

@section('content')

<livewire:students :trashed="$trashed" />

<livewire:add-student-guardians />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-student-modal', () => $('#upsert-student-modal').modal('show'))
    livewire.on('hide-upsert-student-modal', () => $('#upsert-student-modal').modal('hide'))
    livewire.on('hide-add-student-modal', () => $('#add-student-modal').modal('hide'))

    livewire.on('show-delete-student-modal', () => $('#delete-student-modal').modal('show'))
    livewire.on('hide-delete-student-modal', () => $('#delete-student-modal').modal('hide'))

    livewire.on('show-add-student-guardians-modal', () => $('#add-student-guardians-modal').modal('show'))
    livewire.on('hide-add-student-guardians-modal', () => $('#add-student-guardians-modal').modal('hide'))

    livewire.on('hide-import-student-spreadsheet-modal', () => $('#import-student-spreadsheet-modal').modal('hide'))


</script>
@endpush