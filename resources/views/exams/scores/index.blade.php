@extends('layouts.dashboard')

@section('title', "Upload {$exam->name} Scores")

@section('content')

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }} Scores</li>
        </ol>
    </nav>
</div>
<hr>

<div class="row g-4">
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="6">My Classes</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Responsibility</th>
                                <th>Subject</th>
                                <th>Level Unit</th>
                                <th>Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($responsibilities->count())
                            @foreach ($responsibilities as $responsibility)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $responsibility->name }}</td>
                                <td>{{ $responsibility->pivot->subject->name }}</td>
                                <td>{{ $responsibility->pivot->levelUnit->alias }}</td>
                                <td>{{ $responsibility->pivot->level->name }}</td>
                                <td>
                                    <div class="hstack gap-2 align-items-center">
                                        @if ($responsibility->pivot->subject_id && $exam->isInMarking())
                                        <a href="{{ route('exams.scores.upload', [
                                            'exam' => $exam,
                                            'subject' => $responsibility->pivot->subject->id,
                                            'level-unit' => $responsibility->pivot->levelUnit->id,
                                            'level' => $responsibility->pivot->level->id,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-upload"></i>
                                            <span>Upload Scores</span>
                                        </a>
                                        @endif
                                        <a href="{{ route('exams.scores.manage', [
                                            'exam' => $exam,
                                            'subject' => $responsibility->pivot->subject->id,
                                            'level-unit' => $responsibility->pivot->levelUnit->id,
                                            'level' => $responsibility->pivot->level->id,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-cog"></i>
                                            <span>Manage Scores</span>
                                        </a>
                                        @if (($responsibility->name == 'Class Teacher') && $exam->isPublished())
                                        <form action="{{ route('exams.results.send-message', [
                                            'exam' => $exam,
                                            'level-unit' => $responsibility->pivot->levelUnit->id,
                                            'level' => $responsibility->pivot->level->id,
                                        ]) }}" method="post">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                                <i class="fas fa-paper-plane"></i>
                                                <span>Send Results</span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7">
                                    <div class="py-1 text-center">No Responsibility created yet</div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @can('change-exam-status')

    @if ($systemSettings->school_has_streams)
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <x-exams.scores.level-unit-scores :exam="$exam" />
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <x-exams.scores.level-scores :exam="$exam" />
            </div>
        </div>
    </div>
    @endcan
</div>


@endsection

@push('scripts')
<script>

</script>
@endpush