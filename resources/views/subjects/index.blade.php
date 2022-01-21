@extends('layouts.dashboard')

@section('title', 'Subjects')

@section('content')

<livewire:subjects :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-subject-modal', () => $('#upsert-subject-modal').modal('show'))
    livewire.on('hide-upsert-subject-modal', () => $('#upsert-subject-modal').modal('hide'))
    livewire.on('show-delete-subject-modal', () => $('#delete-subject-modal').modal('show'))
    livewire.on('hide-delete-subject-modal', () => $('#delete-subject-modal').modal('hide'))
    livewire.on('show-subject-teachers-modal', () => $('#subject-teachers-modal').modal('show'))
    livewire.on('hide-truncate-subjects-modal', () => $('#truncate-subjects-modal').modal('hide'))
</script>
@endpush