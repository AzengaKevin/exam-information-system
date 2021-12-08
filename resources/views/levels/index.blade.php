@extends('layouts.dashboard')

@section('title', 'Levels')

@section('content')

<div class="d-flex justify-content-between">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Levels</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-level-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Level</span>
    </button>
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

</script>
@endpush