@extends('layouts.dashboard')

@section('title', 'Grading Systems')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Grading Systems</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-grade-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Grade</span>
    </button>
</div>
<hr>

@livewire('grades')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-grade-modal', () => $('#upsert-grade-modal').modal('show'))
    livewire.on('hide-upsert-grade-modal', () => $('#upsert-grade-modal').modal('hide'))
    livewire.on('show-delete-grade-modal', () => $('#delete-grade-modal').modal('show'))
    livewire.on('hide-delete-grade-modal', () => $('#delete-grade-modal').modal('hide'))

</script>
@endpush