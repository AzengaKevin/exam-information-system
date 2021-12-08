@extends('layouts.dashboard')

@section('title', 'Students')

@section('content')

<div class="d-flex justify-content-between">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Students</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-student-modal"
        class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Student</span>
    </button>
</div>
<hr>

<livewire:students />

<livewire:add-student-guardians />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-student-modal', () => $('#upsert-student-modal').modal('show'))
    livewire.on('hide-upsert-student-modal', () => $('#upsert-student-modal').modal('hide'))

    livewire.on('show-delete-student-modal', () => $('#delete-student-modal').modal('show'))
    livewire.on('hide-delete-student-modal', () => $('#delete-student-modal').modal('hide'))

    livewire.on('show-add-student-guardians-modal', () => $('#add-student-guardians-modal').modal('show'))
    livewire.on('hide-add-student-guardians-modal', () => $('#add-student-guardians-modal').modal('hide'))
</script>
@endpush