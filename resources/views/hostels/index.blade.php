@extends('layouts.dashboard')

@section('title', 'Hostels')

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hostels</li>
        </ol>
    </nav>
    <button data-bs-toggle="modal" data-bs-target="#upsert-hostel-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Hostel</span>
    </button>
</div>
<hr>

@livewire('hostels')

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-hostel-modal', () => $('#upsert-hostel-modal').modal('show'))
    livewire.on('hide-upsert-hostel-modal', () => $('#upsert-hostel-modal').modal('hide'))
    livewire.on('show-delete-hostel-modal', () => $('#delete-hostel-modal').modal('show'))
    livewire.on('hide-delete-hostel-modal', () => $('#delete-hostel-modal').modal('hide'))

</script>
@endpush