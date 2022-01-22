@extends('layouts.dashboard')

@section('title', 'Streams')

@section('content')

<livewire:streams :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-stream-modal', () => $('#upsert-stream-modal').modal('show'))
    livewire.on('hide-upsert-stream-modal', () => $('#upsert-stream-modal').modal('hide'))
    livewire.on('show-delete-stream-modal', () => $('#delete-stream-modal').modal('show'))
    livewire.on('hide-delete-stream-modal', () => $('#delete-stream-modal').modal('hide'))
    livewire.on('hide-truncate-streams-modal', () => $('#truncate-streams-modal').modal('hide'))
</script>
@endpush