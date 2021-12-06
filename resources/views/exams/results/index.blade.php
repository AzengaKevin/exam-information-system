@extends('layouts.dashboard')

@section('title', "{$exam->name} Results")

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{ $exam->name }} Results</h1>
</div>

<livewire:exam-results :exam="$exam" />

@endsection

@push('scripts')

@endpush