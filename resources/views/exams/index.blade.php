@extends('layouts.dashboard')

@section('title', 'Exams')

@section('content')

<livewire:exams :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-exam-modal', () => $('#upsert-exam-modal').modal('show'))
    livewire.on('hide-upsert-exam-modal', () => $('#upsert-exam-modal').modal('hide'))

    livewire.on('show-delete-exam-modal', () => $('#delete-exam-modal').modal('show'))
    livewire.on('hide-delete-exam-modal', () => $('#delete-exam-modal').modal('hide'))

    livewire.on('show-enroll-levels-modal', () => $('#enroll-levels-modal').modal('show'))
    livewire.on('hide-enroll-levels-modal', () => $('#enroll-levels-modal').modal('hide'))

    livewire.on('show-enroll-subjects-modal', () => $('#enroll-subjects-modal').modal('show'))
    livewire.on('hide-enroll-subjects-modal', () => $('#enroll-subjects-modal').modal('hide'))
</script>
@endpush