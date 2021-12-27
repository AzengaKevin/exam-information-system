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
            <div class="card-body">
                <div class="table-responsive">

                    @if ($systemSettings->school_has_streams)
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Level</th>
                                <th>Stream</th>
                                <th>Alias</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($levelUnits->count())
                            @foreach ($levelUnits as $levelUnit)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($levelUnit->level)->name }}</td>
                                <td>{{ optional($levelUnit->stream)->name }}</td>
                                <td>{{ $levelUnit->alias ?? 'Not Set' }}</td>
                                <td>
                                    <a href="{{ route('exams.transcripts.show', [
                                        'exam' => $exam,
                                        'level-unit' => $levelUnit->id
                                    ]) }}" class="btn btn-sm btn-outline-primary d-inline-flex gap-2 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Transcripts</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5">
                                    <div class="py-1 text-center">No classes registered to the exam</div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Level</th>
                                <th>Numeric</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($exam->levels->count())
                            @foreach ($exam->levels as $level)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $level->name }}</td>
                                <td>{{ $level->numeric }}</td>
                                <td>
                                    <a href="{{ route('exams.transcripts.show', [
                                        'exam' => $exam,
                                        'level' => $level->id
                                    ]) }}" class="btn btn-sm btn-outline-primary d-inline-flex gap-2 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Transcripts</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">
                                    <div class="py-1 text-center">No levels registered to the exam</div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>

</script>
@endpush