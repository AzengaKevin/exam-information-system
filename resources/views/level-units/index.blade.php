@extends('layouts.dashboard')

@section('title', 'Classes')

@section('content')

<livewire:level-units :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-level-unit-modal', () => $('#upsert-level-unit-modal').modal('show'))
    livewire.on('hide-upsert-level-unit-modal', () => $('#upsert-level-unit-modal').modal('hide'))

    livewire.on('show-delete-level-unit-modal', () => $('#delete-level-unit-modal').modal('show'))
    livewire.on('hide-delete-level-unit-modal', () => $('#delete-level-unit-modal').modal('hide'))
</script>
@endpush

@push('modals')
<x-modals.level-units.generate />
@endpush