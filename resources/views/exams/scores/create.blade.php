@extends('layouts.dashboard')

@section('title', "Upload {$exam->name} {{ $levelUnit->alias }} {{ $subject->name }} Scores")

@section('content')

<div>
    <h1 class="h4 fw-bold text-muted">Upload {{ $exam->name }} {{ $levelUnit->alias }} {{ $subject->name }} Scores</h1>
</div>
<x-feedback />
<hr>

<form action="{{ route('exams.scores.store', [
    'exam' => $exam,
    'subject' => $subject->id,
    'level-unit' => $levelUnit->id,
]) }}" method="post" class="row g-3">
    @csrf
    <div class="col-md-7 col-lg-6">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Adm. No.</th>
                        <th>Name</th>
                        <th>% Score</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($levelUnit->students->count())
                    @foreach ($levelUnit->students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->adm_no }}</td>
                        <td>{{ $student->name }}</td>
                        <td><input type="number" name="scores[{{ $student->adm_no }}]" min="0" max="100"
                                class="form-control form-control-sm"
                                value="{{ old("scores.{$student->adm_no}") ?? $scores[$student->adm_no] ?? null }}">
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="4">Looks like there are no students, in your <strong>{{ $levelUnit->alias }}
                                {{ $subject->name }}</strong> class</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary d-block w-100 btn-lg">Submit</button>
        </div>
    </div>
    <div class="col-md-5 col-lg-6">
        <div class="py-o py-md-3 py-lg-5">
            <label for="grading" class="form-label">Grading System</label>
            <select name="grading_id" id="grading" class="form-select @error('grading_id') is-invalid @enderror">
                <option value="">-- Select --</option>
                @foreach ($gradings as $grading)
                <option value="{{ $grading->id }}">{{ $grading->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>


@endsection

@push('scripts')
<script>

</script>
@endpush