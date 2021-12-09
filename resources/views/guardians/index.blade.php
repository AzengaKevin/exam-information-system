@extends('layouts.dashboard')

@section('title', 'Guardians')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Guardians</li>
        </ol>
    </nav>
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

    livewire.on('show-delete-guardian-modal', () => $('#delete-guardian-modal').modal('show'))
    livewire.on('hide-delete-guardian-modal', () => $('#delete-guardian-modal').modal('hide'))
</script>
@endpush