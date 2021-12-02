@extends('layouts.dashboard')

@section('title', $teacher->auth->name)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{$teacher->auth->name}}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="avatar avatar-5xl avatar-profile"><img class="rounded-circle img-thumbnail shadow-sm"
                src="/profile.png" alt="" width="200"></div>
    </div>
    <div class="col-lg-8">
        <h4 class="mb-1"> {{$teacher->auth->name}}
        </h4>
        <div class="row">
            <div class="col-6">
                <p class="mx-0 my-0">Employer:</p>
                <p class="mx-0 my-0">Tsc No:</p>
                <p class="mx-0 my-0">Responsibilities:</p>
                <p class="mx-0 my-0">Tasks:</p>
            </div>
            <div class="col-6">
                <p class="mx-0 my-0">My classes:</p>
                <p class="mx-0 my-0">Stream:</p>

                <p class="mx-0 my-0">Description</p>
            
            </div>
        </div>
    </div>
</div>

<div class="row">
    <h4 class="font-weight-bold text-center">Current Exams</h4>
    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Shortname</th>
                    <th>Year</th>
                    <th>Term</th>
                    <th>StartDate</th>
                    <th>EndDate</th>
                    <th>Weight</th>
                    <th>Counts</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($currentExams->count())
                @foreach ($currentExams as $exam)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exam->shortname }}</td>
                    <td>{{ $exam->year }}</td>
                    <td>{{ $exam->term }}</td>
                    <td>{{ $exam->start_date }}</td>
                    <td>{{ $exam->end_date }}</td>
                    <td>{{ $exam->weight }}</td>
                    <td>{{ $exam->counts ? 'True' : 'False' }}</td>
                    <td>{{ $exam->status }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <a href="{{route('teachers.currentExamMarking',['teacher'=>$teacher,'exam'=>$exam])}}" class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="10">
                        <div class="py-1">No Exam created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>


@endsection