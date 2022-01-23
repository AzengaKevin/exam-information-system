@extends('layouts.dashboard')

@section('title', 'Responsibilities')

@section('content')

<livewire:responsibilities :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-responsibility-modal', () => $('#upsert-responsibility-modal').modal('show'))
    livewire.on('hide-upsert-responsibility-modal', () => $('#upsert-responsibility-modal').modal('hide'))
    livewire.on('show-delete-responsibility-modal', () => $('#delete-responsibility-modal').modal('show'))
    livewire.on('hide-delete-responsibility-modal', () => $('#delete-responsibility-modal').modal('hide'))

</script>
@endpush