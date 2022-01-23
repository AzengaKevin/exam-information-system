@extends('layouts.dashboard')

@section('title', 'Hostels')

@section('content')

<livewire:hostels :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-hostel-modal', () => $('#upsert-hostel-modal').modal('show'))
    livewire.on('hide-upsert-hostel-modal', () => $('#upsert-hostel-modal').modal('hide'))
    livewire.on('show-delete-hostel-modal', () => $('#delete-hostel-modal').modal('show'))
    livewire.on('hide-delete-hostel-modal', () => $('#delete-hostel-modal').modal('hide'))

</script>
@endpush