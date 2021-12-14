@extends('layouts.dashboard')

@section('title', 'Subjects')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Subjects</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-subject-modal"
        class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Subject</span>
    </button>
</div>
<hr>

@livewire('subjects')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-subject-modal', () => $('#upsert-subject-modal').modal('show'))
    livewire.on('hide-upsert-subject-modal', () => $('#upsert-subject-modal').modal('hide'))
    livewire.on('show-delete-subject-modal', () => $('#delete-subject-modal').modal('show'))
    livewire.on('hide-delete-subject-modal', () => $('#delete-subject-modal').modal('hide'))

    livewire.on('show-subject-teachers-modal', () => $('#subject-teachers-modal').modal('show'))
</script>
@endpush