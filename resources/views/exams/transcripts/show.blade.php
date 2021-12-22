@extends('layouts.dashboard')

@section('title', "{$exam->name} - {$levelUnit->alias} - Transcripts")

@section('content')

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('exams.transcripts.index',$exam) }}">{{ $exam->name }} Transcripts</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }} - {{$levelUnit->alias}} Transcripts
            </li>
        </ol>
    </nav>
    <a href="{{ route('exams.transcripts.print-bulk', [
        'exam' => $exam,
        'level-unit' => $levelUnit->id
    ]) }}" class="btn btn-primary f-inline-flex gap-2 align-items-center" download>
        <i class="fa fa-download"></i>
        <span>Download All</span>
    </a>
</div>

<div class="row g-4 py-3">

    @foreach ($studentsScores as $studentScores)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary bg-gradient text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5>{{ $studentScores->name }}</h5>
                    <a href="{{ route('exams.transcripts.print-one', [
                        'exam' => $exam,
                        'admno' => $studentScores->adm_no
                    ]) }}" class="btn btn-light d-inline-flex gap-2 align-items-center">
                        <i class="fa fa-download"></i>
                        <span>Transcript</span>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <x-exams.transcript :exam="$exam" :studentScores="$studentScores" :outOfs="$outOfs"
                    :subjectColumns="$subjectColumns" :subjectsMap="$subjectsMap" :swahiliComments="$swahiliComments"
                    :ctComments="$ctComments" :pComments="$pComments" :englishComments="$englishComments"
                    :teachers="$teachers" />
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>

</script>
@endpush