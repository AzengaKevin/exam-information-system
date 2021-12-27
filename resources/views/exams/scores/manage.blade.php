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
</div>
<x-feedback />
<hr>

@if ($subject)
@include('partials.exams.scores.manage.subject')
@elseif($levelUnit)
@include('partials.exams.scores.manage.class')
@elseif($level)
@include('partials.exams.scores.manage.level')
@else
<p class="lead">You are in the wrong place, start again from <a href="{{ route('dashboard') }}">the dashboard</a></p>
@endif

@endsection

@push('scripts')
<script>
    livewire.on('hide-generate-scores-aggregates-modal', () => $('#generate-scores-aggregates-modal').modal('hide'));
    livewire.on('show-generate-scores-aggregates-modal', () => $('#generate-scores-aggregates-modal').modal('show'));

    livewire.on('hide-publish-class-scores-modal', () =>
        $('#publish-class-scores-modal').modal('hide'));
    livewire.on('hide-publish-level-grade-dist-modal', () =>
        $('#publish-level-grade-dist-modal').modal('hide'));

    livewire.on('hide-publish-subjects-performance-modal', () =>
        $('#publish-subjects-performance-modal').modal('hide'));
    livewire.on('hide-rank-class-modal', () => $('#rank-class-modal').modal('hide'));

    livewire.on('hide-publish-students-results-modal', () =>
        $('#publish-students-results-modal').modal('hide'))
</script>
@endpush