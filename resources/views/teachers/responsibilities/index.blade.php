@extends('layouts.dashboard')

@section('title', 'Teacher Responsibilities')

@section('content')

<div class="d-flex justify-content-between">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $teacher->auth->name }} Responsibilites</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#assign-teacher-responsibility-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Responsibility</span>
    </button>
</div>
<hr>

<livewire:teacher-responsibilities :teacher="$teacher" />

@endsection

@push('scripts')
<script>
    livewire.on('hide-assign-teacher-responsibility-modal', () => $('#assign-teacher-responsibility-modal').modal('hide'));
</script>
@endpush