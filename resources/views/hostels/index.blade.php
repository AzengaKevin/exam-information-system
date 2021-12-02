@extends('layouts.dashboard')

@section('title', 'Hostels')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Hostels</h1>
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