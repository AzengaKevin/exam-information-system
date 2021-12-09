@extends('layouts.dashboard')

@section('title', 'Departments')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Departments</li>
        </ol>
    </nav>
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