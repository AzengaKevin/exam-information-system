@extends('layouts.dashboard')

@section('title', $levelUnit->alias)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{$levelUnit->alias}}</h1>
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Adm</th>
                <th>Marks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levelUnit->students as $student)
            <tr>
                <td scope="row">{{$loop->iteration}}</td>
                <td>{{$student->adm_no }}</td>
                <td>
                  <input type="text" name="" id="" class="form-control" placeholder="" aria-describedby="helpId">
            </td>
                <td>
                    <div class="hstack gap-2 align-items-center justify-content-center">
                    <a class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
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