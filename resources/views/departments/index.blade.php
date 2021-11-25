@extends('layouts.dashboard')

@section('title', 'Departments')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Departmetns</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-department-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Department</span>
    </button>
</div>
<hr>

@livewire('departments')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-department-modal', () => $('#upsert-department-modal').modal('show'))
    livewire.on('hide-upsert-department-modal', () => $('#upsert-department-modal').modal('hide'))
    livewire.on('show-delete-department-modal', () => $('#delete-department-modal').modal('show'))
    livewire.on('hide-delete-department-modal', () => $('#delete-department-modal').modal('hide'))

</script>
@endpush