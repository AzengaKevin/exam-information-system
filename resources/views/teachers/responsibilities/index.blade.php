@extends('layouts.dashboard')

@section('title', 'Teacher Responsibilities')

@section('content')

<div class="d-flex justify-content-between align-items-center">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $teacher->auth->name }} Responsibilites</li>
        </ol>
    </nav>

    <div class="d-inline-flex gap-2">
        <button data-bs-toggle="modal" data-bs-target="#assign-teacher-responsibility-modal"
            class="btn btn-sm btn-outline-primary rounded-circle d-md-none">
            <i class="fa fa-plus"></i>
        </button>


        <button data-bs-toggle="modal" data-bs-target="#assign-teacher-responsibility-modal"
            class="d-none d-md-inline-flex btn btn-sm btn-outline-primary gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Responsibility</span>
        </button>

        @if ($systemSettings->school_has_streams)
        <button data-bs-toggle="modal" data-bs-target="#assign-bulk-responsibilities-modal"
            class="btn btn-sm btn-outline-primary rounded-circle d-md-none">
            <i class="fa fa-plus"></i>
        </button>
        <button data-bs-toggle="modal" data-bs-target="#assign-bulk-responsibilities-modal"
            class="d-none d-md-inline-flex btn btn-sm btn-outline-primary gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Bulk Responsibilities</span>
        </button>
        @endif
    </div>
</div>
<hr>

<livewire:teacher-responsibilities :teacher="$teacher" />

@endsection

@push('scripts')
<script>
    livewire.on('hide-assign-teacher-responsibility-modal', () =>
        $('#assign-teacher-responsibility-modal').modal('hide'));

    livewire.on('hide-assign-bulk-responsibilities-modal', () =>
        $('#assign-bulk-responsibilities-modal').modal('hide'));
</script>
@endpush