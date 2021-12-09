@extends('layouts.dashboard')

@section('title', 'Exams')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Exams</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-exam-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Exam</span>
    </button>
</div>
<hr>

@livewire('exams')

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