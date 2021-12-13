@extends('layouts.dashboard')

@section('title', $title)

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.scores.index', $exam) }}">{{ $exam->name }} Scores</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    @if (!is_null($levelUnit) && is_null($subject))
    <div class="dropdown">
        <button type="button" class="btn text-nowrap btn-outline-primary dropdown-toggle" id="exam-class-action-button"
            data-bs-toggle="dropdown" aria-expanded="false">Class Actions</button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exam-class-action-button">
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
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#rank-class-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-sort-amount-down"></i>
                    <span>Rank</span>
                </a>
            </li>
        </ul>
    </div>
    @elseif($level)

    <div class="dropdown">
        <button type="button" class="btn btn-outline-primary text-nowrap dropdown-toggle" id="exam-class-action-button"
            data-bs-toggle="dropdown" aria-expanded="false">Level Actions</button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exam-class-action-button">
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#publish-level-grade-dist-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-upload"></i>
                    <span>Publish Grade Dist.</span>
                </a>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#publish-class-scores-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-upload"></i>
                    <span>Publish Scores</span>
                </a>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#publish-subjects-performance-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-upload"></i>
                    <span>Publish Subject Performance.</span>
                </a>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#rank-class-modal" role="button"
                    class="dropdown-item hstack gap-2">
                    <i class="fa fa-sort-amount-down"></i>
                    <span>Rank</span>
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
    livewire.on('hide-publish-level-grade-dist-modal', () => $('#publish-level-grade-dist-modal').modal('hide'));

    livewire.on('hide-publish-subjects-performance-modal', () => $('#publish-subjects-performance-modal').modal(
        'hide'));
    livewire.on('hide-rank-class-modal', () => $('#rank-class-modal').modal('hide'))
</script>
@endpush