@extends('layouts.dashboard')

@section('title', !is_null($subject) ? "Upload {$exam->name} {$levelUnit->alias} {$subject->name} Scores" : "{$levelUnit->alias} Scores Management")

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <h1 class="h4 fw-bold text-muted">{{ !is_null($subject) ? "Upload {$exam->name} {$levelUnit->alias} {$subject->name} Scores" : "{$levelUnit->alias} Scores Management" }}</h1>
    
    @if (is_null($subject))
    <button type="button" data-bs-toggle="modal" data-bs-target="#generate-scores-aggreagetes-modal" class="btn btn-sm btn-outline-primary hstack gap-2">
        <i class="fa fa-calculator"></i>
        <span>Aggregates</span>
    </button>
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
</script>
@endpush