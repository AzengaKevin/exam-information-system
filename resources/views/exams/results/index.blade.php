@extends('layouts.dashboard')

@section('title', "{$exam->name} Results")

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }} Results</li>
        </ol>
    </nav>
</div>

<hr>

{{-- <livewire:exam-results :exam="$exam" /> --}}

<div class="row g-4">
    @foreach ($exam->levels as $level)
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <livewire:level-exam-results :exam="$exam" :level="$level" />
            </div>
        </div>
    </div>
    @endforeach
</div>


@endsection

@push('scripts')

@endpush