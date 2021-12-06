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
    <div class="col-md-7 col-lg-5">
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
                        <td><input type="number" name="scores[{{ $student->adm_no }}]" min="0" max="100" class="form-control form-control-sm"></td>
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
</form>


@endsection

@push('scripts')
<script>

</script>
@endpush