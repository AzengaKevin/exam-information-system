@extends('layouts.dashboard')

@section('title', "{$exam->name} Results")

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }} Results</li>
        </ol>
    </nav>

    <button data-bs-toggle="modal" data-bs-target="#filter-exam-results"
        class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-filter"></i>
        <span>Filter Results</span>
    </button>
</div>

<hr>

<livewire:exam-results :exam="$exam" />

@endsection

@push('scripts')

@endpush