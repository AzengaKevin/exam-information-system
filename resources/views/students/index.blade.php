@extends('layouts.dashboard')

@section('title', 'Students')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Students</li>
        </ol>
    </nav>
    <div class="hstack flex-wrap gap-2">

        @if ($systemSettings->school_level == 'secondary')            
        <button data-bs-toggle="modal" data-bs-target="#import-student-spreadsheet-modal"
            class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-file-upload"></i>
            <span>Import</span>
        </button>
        <button data-bs-toggle="modal" data-bs-target="#export-student-spreadsheet-modal"
            class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-file-excel"></i>
            <span>Download</span>
        </button>
        @endif

        <button data-bs-toggle="modal" data-bs-target="#upsert-student-modal"
            class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Student</span>
        </button>
    </div>
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

    livewire.on('hide-import-student-spreadsheet-modal', () => $('#import-student-spreadsheet-modal').modal('hide'))

</script>
@endpush