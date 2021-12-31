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
                                        @if ($responsibility->pivot->subject_id)
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
                                        @if ($responsibility->name == 'Class Teacher')
                                        <a href="#"
                                            class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-send"></i>
                                            <span>Send Results</span>
                                        </a>
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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="3">All Classes</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($levelUnits->count())
                            @foreach ($levelUnits as $levelUnit)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $levelUnit->alias }}</td>
                                <td>
                                    <div class="d-inline-flex gap-2 align-items-center">
                                        <a href="{{ route('exams.scores.manage', [
                                            'exam' => $exam,
                                            'level-unit' => $levelUnit->id
                                        ]) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-cog"></i>
                                            <span>Manage Class</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="3">All Levels</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($levels->count())
                            @foreach ($levels as $level)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $level->name }}</td>
                                <td>
                                    <div class="d-inline-flex gap-2 align-items-center">

                                        <a href="{{ route('exams.scores.manage', [
                                            'exam' => $exam,
                                            'level' => $level->id
                                        ]) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-cog"></i>
                                            <span>Manage Level</span>
                                        </a>
                                    </div>

                                </td>
                            </tr>
                            @endforeach
                            @else
                            @endif
                        </tbody>
                    </table>
                </div>
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