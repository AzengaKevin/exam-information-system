@extends('layouts.dashboard')

@section('title', 'Teacher Responsibilities')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{ $teacher->auth->name }} Responsibilites</h1>
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

</script>
@endpush