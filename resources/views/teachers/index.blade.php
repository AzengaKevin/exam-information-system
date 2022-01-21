@extends('layouts.dashboard')

@section('title', 'Teachers')

@section('content')

<livewire:teachers :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-teacher-modal', () => $('#upsert-teacher-modal').modal('show'))
    livewire.on('hide-upsert-teacher-modal', () => $('#upsert-teacher-modal').modal('hide'))

    livewire.on('show-delete-teacher-modal', () => $('#delete-teacher-modal').modal('show'))
    livewire.on('hide-delete-teacher-modal', () => $('#delete-teacher-modal').modal('hide'))
</script>
@endpush