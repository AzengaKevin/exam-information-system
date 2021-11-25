@extends('layouts.dashboard')

@section('title', 'Permissions')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Permissions</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-permission-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Permission</span>
    </button>
</div>
<hr>

@livewire('permissions')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-permission-modal', () => $('#upsert-permission-modal').modal('show'))
    livewire.on('hide-upsert-permission-modal', () => $('#upsert-permission-modal').modal('hide'))
    livewire.on('show-delete-permission-modal', () => $('#delete-permission-modal').modal('show'))
    livewire.on('hide-delete-permission-modal', () => $('#delete-permission-modal').modal('hide'))

</script>
@endpush