@extends('layouts.dashboard')

@section('title', !is_null($subject) ? "Upload {$exam->name} {$levelUnit->alias} {$subject->name} Scores" : "{$levelUnit->alias} Scores Management")

@section('content')

<div>
    <h1 class="h4 fw-bold text-muted">{{ !is_null($subject) ? "Upload {$exam->name} {$levelUnit->alias} {$subject->name} Scores" : "{$levelUnit->alias} Scores Management" }}</h1>
</div>
<x-feedback />
<hr>

@if ($subject)
@include('partials.exams.upload-scores')
@else
@endif

@endsection

@push('scripts')
<script>

</script>
@endpush