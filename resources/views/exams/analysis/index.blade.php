@extends('layouts.dashboard')

@section('title', $title)

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            @if (request()->has('level'))
            <li class="breadcrumb-item"><a href="{{ route('exams.analysis.index', $exam) }}">{{ $exam->name }} Analysis</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>
</div>

@if ($level)
@include('partials.exams.analysis.level')
@else
<div class="row g-4 py-3">
    @foreach ($exam->levels as $level)
    <div class="col-md-12">
        <x-exams.analysis.level-line-graph :exam="$exam" :level="$level" />
    </div>
    @endforeach
</div>
@endif

@endsection