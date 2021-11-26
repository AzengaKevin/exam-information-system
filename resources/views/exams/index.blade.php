@extends('layouts.dashboard')

@section('title', 'Exams')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Exams</h1>
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

</script>
@endpush