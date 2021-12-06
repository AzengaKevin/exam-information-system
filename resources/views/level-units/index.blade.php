@extends('layouts.dashboard')

@section('title', 'Level Units')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Classes</h1>

    <div class="hstack gap-2">
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
                <span>Level Unit</span>
            </button>
            <button data-bs-toggle="modal" data-bs-target="#generate-level-unit-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-cog"></i>
                <span>Generate</span>
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