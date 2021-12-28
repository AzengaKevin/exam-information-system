@extends('layouts.dashboard')

@section('title', 'Levels')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @if ($systemSettings->school_has_streams)
            <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">Levels</li>
        </ol>
    </nav>
    <div class="d-inline-flex gap-2 align-items-center flex-wrap">
        <button type="button" data-bs-toggle="modal" data-bs-target="#upsert-level-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Level</span>
        </button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#truncate-levels-modal" class="btn btn-outline-danger d-inline-flex gap-2 align-items-center">
            <i class="fa fa-trash"></i>
            <span>Delete All Levels</span>
        </button>
    </div>
</div>
<hr>

@livewire('levels')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-level-modal', () => $('#upsert-level-modal').modal('show'))
    livewire.on('hide-upsert-level-modal', () => $('#upsert-level-modal').modal('hide'))
    livewire.on('show-delete-level-modal', () => $('#delete-level-modal').modal('show'))
    livewire.on('hide-delete-level-modal', () => $('#delete-level-modal').modal('hide'))
    livewire.on('hide-truncate-levels-modal', () => $('#truncate-levels-modal').modal('hide'))

</script>
@endpush