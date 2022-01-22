@extends('layouts.dashboard')

@section('title', 'Levels')

@section('content')

<livewire:levels :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-level-modal', () => $('#upsert-level-modal').modal('show'))
    livewire.on('hide-upsert-level-modal', () => $('#upsert-level-modal').modal('hide'))
    livewire.on('show-delete-level-modal', () => $('#delete-level-modal').modal('show'))
    livewire.on('hide-delete-level-modal', () => $('#delete-level-modal').modal('hide'))
    livewire.on('hide-truncate-levels-modal', () => $('#truncate-levels-modal').modal('hide'))

</script>
@endpush