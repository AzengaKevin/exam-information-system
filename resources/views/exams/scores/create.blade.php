@extends('layouts.dashboard')

@section('title', !is_null($subject) ? "Upload {$exam->name} {$levelUnit->alias} {$subject->name} Scores" :
"{$levelUnit->alias} Scores Management")

@section('content')

<div class="d-flex justify-content-between align-items-center">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.scores.index', $exam) }}">{{ $exam->name }} Scores</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ !is_null($subject) ? "Upload {$exam->name} {$levelUnit->alias} {$subject->name} Scores" : "{$levelUnit->alias} Scores Management" }}
            </li>
        </ol>
    </nav>

    @if (is_null($subject))
    <div class="dropdown">
        <button type="button" class="btn btn-outline-primary" id="exam-class-action-button" data-bs-toggle="dropdown"
            aria-expanded="false">Class Actions</button>
        <ul class="dropdown-menu dropdwon-menu-end" aria-labelledby="exam-class-action-button">
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#generate-scores-aggreagetes-modal" role="button"
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
@include('partials.exams.upload-scores')
@else
@include('partials.exams.generate-aggregates')
@endif

@endsection

@push('scripts')
<script>
    livewire.on('hide-generate-scores-aggreagetes', () => $('#generate-scores-aggreagetes-modal').modal('hide'));
    livewire.on('show-generate-scores-aggreagetes', () => $('#generate-scores-aggreagetes-modal').modal('show'));

    livewire.on('hide-publish-class-scores-modal', () => $('#publish-class-scores-modal').modal('hide'));
</script>
@endpush