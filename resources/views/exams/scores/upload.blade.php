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
<form action="{{ route('exams.scores.upload', [
    'exam' => $exam,
    'subject' => $subject->id,
    'level-unit' => optional($levelUnit)->id,
    'level' => optional($level)->id
]) }}" method="post" class="row g-3">
    @csrf
    @method('PUT')
    <div class="col-md-8">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Adm. No.</th>
                        <th>% Score</th>
                        <th>Missing / Cheated</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data->count())
                    @foreach ($data as $item)
                    @php
                    $col = $subject->shortname;
                    $score = json_decode($item->$col);
                    @endphp
                    <tr>
                        <td>{{ $item->stid }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->admno }}</td>
                        <td><input type="number" name="scores[{{ $item->stid }}][score]" min="0" max="100"
                                class="form-control form-control-sm"
                                value="{{ old("scores.{$item->stid}.score") ?? optional($score)->score }}">
                        </td>
                        <td>
                            <select name="scores[{{ $item->stid }}][extra]" class="form-select form-select-sm">
                                <option value="">-- Select --</option>
                                <option value="missing" {{ optional($score)->grade == 'X' }}>Missing</option>
                                <option value="cheated" {{ optional($score)->grade == 'Y' }}>Cheated</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5">Looks like there are no students, in your
                            <strong>{{ optional($level)->name ?? optional($levelUnit)->alias }} -
                                {{ $subject->name }} Class</strong></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($systemSettings->school_level == 'secondary')
        <div class="mt-3">
            <label for="grading" class="form-label">Grading System</label>
            <select name="grading_id" id="grading" class="form-select @error('grading_id') is-invalid @enderror">
                <option value="">-- Select --</option>
                @foreach ($gradings as $grading)
                <option value="{{ $grading->id }}">{{ $grading->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="mt-3">
            <button type="submit" class="btn btn-primary d-block w-100 btn-lg">Submit</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>

</script>
@endpush