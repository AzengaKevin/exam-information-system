@extends('layouts.dashboard')

@section('title', 'Students')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Students</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-student-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Student</span>
    </button>
</div>
<hr>

<livewire:students />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-student-modal', () => $('#upsert-student-modal').modal('show'))
    livewire.on('hide-upsert-student-modal', () => $('#upsert-student-modal').modal('hide'))

    livewire.on('show-delete-student-modal', () => $('#delete-student-modal').modal('show'))
    livewire.on('hide-delete-student-modal', () => $('#delete-student-modal').modal('hide'))
</script>
@endpush