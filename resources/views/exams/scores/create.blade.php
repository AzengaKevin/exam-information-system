@extends('layouts.dashboard')

@section('title', $title)

@section('content')

<div class="d-flex justify-content-between align-items-center">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.scores.index', $exam) }}">{{ $exam->name }} Scores</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    @if (!is_null($levelUnit))
    <div class="dropdown">
        <button type="button" class="btn btn-outline-primary" id="exam-class-action-button" data-bs-toggle="dropdown"
            aria-expanded="false">Class Actions</button>
        <ul class="dropdown-menu dropdwon-menu-end" aria-labelledby="exam-class-action-button">
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#generate-scores-aggregates-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-calculator"></i>
                    <span>Aggregates</span>
                </a>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#publish-class-scores-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-upload"></i>
                    <span>Publish</span>
                </a>
            </li>
        </ul>
    </div>
    @endif
</div>
<x-feedback />
<hr>

@if ($subject)
@include('partials.exams.scores.create.subject')
@elseif($levelUnit)
@include('partials.exams.scores.create.class')
@elseif($level)
@include('partials.exams.scores.create.level')
@else
<p class="lead">You are in the wrong place, start again from <a href="{{ route('dashboard') }}">the dashboard</a></p>
@endif

@endsection

@push('scripts')
<script>
    livewire.on('hide-generate-scores-aggregates-modal', () => $('#generate-scores-aggregates-modal').modal('hide'));
    livewire.on('show-generate-scores-aggregates-modal', () => $('#generate-scores-aggregates-modal').modal('show'));

    livewire.on('hide-publish-class-scores-modal', () => $('#publish-class-scores-modal').modal('hide'));
</script>
@endpush