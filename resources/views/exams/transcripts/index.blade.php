@extends('layouts.dashboard')

@section('title', "{$exam->name} Transcripts")

@section('content')

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }} Transcripts</li>
        </ol>
    </nav>
</div>

<div class="row g-4 py-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5>Name Report Form</h5>
                    <div class="d-inline-flex">
                        <a href="{{ route('exams.transcripts.print', [
                            'exam' => $exam,
                            'admno' => request()->admno
                        ]) }}" class="btn btn-primary">Download</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($students)
                @include('partials.exams.transcripts.students')
                @else
                @if ($studentScores)
                @include('partials.exams.transcripts.form')
                @else
                <p>Can't find student score for current exam</p>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>

</script>
@endpush