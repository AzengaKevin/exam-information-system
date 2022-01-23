@extends('layouts.dashboard')

@section('title', 'Grading Systems')

@section('content')

<livewire:gradings :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-grading-modal', () => $('#upsert-grading-modal').modal('show'))
    livewire.on('hide-upsert-grading-modal', () => $('#upsert-grading-modal').modal('hide'))
    livewire.on('show-delete-grading-modal', () => $('#delete-grading-modal').modal('show'))
    livewire.on('hide-delete-grading-modal', () => $('#delete-grading-modal').modal('hide'))

    livewire.on('show-grading-instance-modal', () => $('#grading-instance-modal').modal('show'))
</script>
@endpush