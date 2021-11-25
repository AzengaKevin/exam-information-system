@extends('layouts.dashboard')

@section('title', 'Guardians')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Guardians</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-guardian-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Guardian</span>
    </button>
</div>
<hr>

<livewire:guardians />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-guardian-modal', () => $('#upsert-guardian-modal').modal('show'))
    livewire.on('hide-upsert-guardian-modal', () => $('#upsert-guardian-modal').modal('hide'))
</script>
@endpush