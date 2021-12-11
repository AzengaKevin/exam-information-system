@extends('layouts.dashboard')

@section('title', 'Teachers')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Teachers</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-teacher-modal" class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Teacher</span>
    </button>
</div>
<hr>

<livewire:teachers />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-teacher-modal', () => $('#upsert-teacher-modal').modal('show'))
    livewire.on('hide-upsert-teacher-modal', () => $('#upsert-teacher-modal').modal('hide'))

    livewire.on('show-delete-teacher-modal', () => $('#delete-teacher-modal').modal('show'))
    livewire.on('hide-delete-teacher-modal', () => $('#delete-teacher-modal').modal('hide'))
</script>
@endpush