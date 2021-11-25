@extends('layouts.dashboard')

@section('title', 'Streams')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Streams</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-stream-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
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