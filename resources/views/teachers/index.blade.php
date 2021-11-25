@extends('layouts.dashboard')

@section('title', 'Teachers')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Teachers</h1>
    <button class="btn btn-outline-primary hstack gap-2 align-items-center">
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
</script>
@endpush