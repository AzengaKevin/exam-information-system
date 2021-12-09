@extends('layouts.dashboard')

@section('title', 'Grading Systems')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Grading Systems</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-grading-modal"
        class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>System</span>
    </button>
</div>
<hr>

<livewire:gradings />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-grading-modal', () => $('#upsert-grading-modal').modal('show'))
    livewire.on('hide-upsert-grading-modal', () => $('#upsert-grading-modal').modal('hide'))
    livewire.on('show-delete-grading-modal', () => $('#delete-grading-modal').modal('show'))
    livewire.on('hide-delete-grading-modal', () => $('#delete-grading-modal').modal('hide'))

    livewire.on('show-grading-instance-modal', () => $('#grading-instance-modal').modal('show'))
</script>
@endpush