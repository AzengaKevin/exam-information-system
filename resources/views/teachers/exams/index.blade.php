@extends('layouts.dashboard')

@section('title', $teacher->auth->name)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{$teacher->auth->name}}</h1>
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($responsibilities as $responsibility)
            <tr>
                <td scope="row">{{$loop->iteration}}</td>
                <td>{{$responsibility->pivot->levelUnit->alias }}</td>
                <td>{{$responsibility->pivot->subject->name }}</td>
                <td>
                    <div class="hstack gap-2 align-items-center justify-content-center">
                    <a href="{{route('teacher.studentToBeScored',['levelUnit'=>$responsibility->pivot->levelUnit,'subject'=>$responsibility->pivot->subject])}}" class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                        <i class="fa fa-eye"></i>
                        <span>Details</span>
                    </a>
                    </div>
                </td>
            </tr>            
            @endforeach
        </tbody>
    </table>
</div>


@endsection