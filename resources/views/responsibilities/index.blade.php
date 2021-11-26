@extends('layouts.dashboard')

@section('title', 'Responsibilities')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Responsibilites</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-responsibility-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
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