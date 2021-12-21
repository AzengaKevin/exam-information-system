@extends('layouts.dashboard')

@section('title', 'Grades, Points & Comments')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Grades, Points & Comments</li>
        </ol>
    </nav>
</div>
<hr>

<livewire:grades />

@endsection

@push('scripts')
<script>
    livewire.on('show-update-grade-modal', () => $('#update-grade-modal').modal('show'))
    livewire.on('hide-update-grade-modal', () => $('#update-grade-modal').modal('hide'))
</script>
@endpush