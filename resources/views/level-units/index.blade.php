@extends('layouts.dashboard')

@section('title', 'Classes')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between alig-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Classes</li>
        </ol>
    </nav>

    <div class="d-inline-flex gap-2 flex-column flex-md-row align-items-start align-items-md-center">
        <div class="btn-group">
            <a href="{{ route('levels.index') }}" class="btn btn-outline-primary hstack gap-1 align-items-center">
                <i class="fa fa-list-ul"></i>
                <span>Levels</span>
            </a>
            <a href="{{ route('streams.index') }}" class="btn btn-outline-primary hstack gap-1 align-items-center">
                <i class="fa fa-list-ul"></i>
                <span>Streams</span>
            </a>
        </div>
        <div class="btn-group">
            <button data-bs-toggle="modal" data-bs-target="#upsert-level-unit-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Class</span>
            </button>
            <button data-bs-toggle="modal" data-bs-target="#generate-level-unit-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-cog"></i>
                <span>Generate Classes</span>
            </button>
        </div>
    </div>
</div>
<hr>

<livewire:level-units />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-level-unit-modal', () => $('#upsert-level-unit-modal').modal('show'))
    livewire.on('hide-upsert-level-unit-modal', () => $('#upsert-level-unit-modal').modal('hide'))

    livewire.on('show-delete-level-unit-modal', () => $('#delete-level-unit-modal').modal('show'))
    livewire.on('hide-delete-level-unit-modal', () => $('#delete-level-unit-modal').modal('hide'))
</script>
@endpush

@push('modals')
<x-modals.level-units.generate />
@endpush