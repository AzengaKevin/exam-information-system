@extends('layouts.dashboard')

@section('title', 'Streams')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Streams</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-stream-modal"
        class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Stream</span>
    </button>
</div>
<hr>

@livewire('streams')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-stream-modal', () => $('#upsert-stream-modal').modal('show'))
    livewire.on('hide-upsert-stream-modal', () => $('#upsert-stream-modal').modal('hide'))
    livewire.on('show-delete-stream-modal', () => $('#delete-stream-modal').modal('show'))
    livewire.on('hide-delete-stream-modal', () => $('#delete-stream-modal').modal('hide'))
</script>
@endpush