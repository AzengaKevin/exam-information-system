@extends('layouts.dashboard')

@section('title', 'Responsibilities')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Responsibilities</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-responsibility-modal" class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Responsibility</span>
    </button>
</div>
<hr>

@livewire('responsibilities')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-responsibility-modal', () => $('#upsert-responsibility-modal').modal('show'))
    livewire.on('hide-upsert-responsibility-modal', () => $('#upsert-responsibility-modal').modal('hide'))
    livewire.on('show-delete-responsibility-modal', () => $('#delete-responsibility-modal').modal('show'))
    livewire.on('hide-delete-responsibility-modal', () => $('#delete-responsibility-modal').modal('hide'))

</script>
@endpush