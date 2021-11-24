@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Users</h1>
</div>
<hr>

<livewire:users />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-user-modal', () => $('#upsert-user-modal').modal('show'))
    livewire.on('hide-upsert-user-modal', () => $('#upsert-user-modal').modal('hide'))
</script>
@endpush