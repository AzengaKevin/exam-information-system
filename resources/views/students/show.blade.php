@extends('layouts.dashboard')

@section('title', $student->name)

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $student->name }}</li>
        </ol>
    </nav>
</div>

<div class="row g-4 py-3">

    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <h5>Basic Details</h5>
                <hr>
                <div class="row g-2 align-items-center">
                    <dl class="col-md-4">
                        <dt>Name</dt>
                        <dd>{{ $student->name }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>Admission Number</dt>
                        <dd>{{ $student->adm_no }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>UPI</dt>
                        <dd>{{ $student->upi  ?? 'N/A'}}</dd>
                    </dl>
                    @if ($systemSettings->school_level == 'secondary')
                    <dl class="col-md-4">
                        <dt>KCPE Marks</dt>
                        <dd>{{ $student->kcpe_marks }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>KCPE Grade</dt>
                        <dd>{{ $student->kcpe_grade }}</dd>
                    </dl>
                    @endif
                    <dl class="col-md-4">
                        <dt>Gender</dt>
                        <dd>{{ $student->gender }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>Age</dt>
                        <dd>{{ $student->dob->diffInYears(now()) }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>Level</dt>
                        <dd>{{ $student->level->name }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>Stream</dt>
                        <dd>{{ $student->stream->name }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>Class</dt>
                        <dd>{{ optional($student->levelUnit)->alias }}</dd>
                    </dl>
                    <dl class="col-md-4">
                        <dt>Join Level</dt>
                        <dd>{{ optional($student->admissionLevel)->name ?? 'N/A' }}</dd>
                    </dl>
                    <dl class="col-md-12">
                        <dt>Description</dt>
                        <dd>{{ $student->description ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5>Student Guardians</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Profession</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($student->guardians->count())
                            @foreach ($student->guardians as $guardian)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($guardian->auth)->name ?? '-' }}</td>
                                <td>{{ $guardian->profession }}</td>
                                <td>{{ $guardian->location }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">The student has no guardian set</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection