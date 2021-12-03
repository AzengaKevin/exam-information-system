@extends('layouts.dashboard')

@section('title', "Upload {$exam->name} {{ $levelUnit->alias }} {{ $subject->name }} Scores")

@section('content')

<div>
    <h1 class="h4 fw-bold text-muted">Upload {{ $exam->name }} {{ $levelUnit->alias }} {{ $subject->name }} Scores</h1>
</div>
<hr>

<form action="" method="post" class="row g-3">
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
                    @foreach ($levelUnit->students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->adm_no }}</td>
                        <td>{{ $student->name }}</td>
                        <td><input type="number" min="0" max="100" class="form-control form-control-sm"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>


@endsection

@push('scripts')
<script>

</script>
@endpush