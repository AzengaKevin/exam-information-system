@extends('layouts.dashboard')

@section('title', $student->name)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{$student->name}}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="avatar avatar-5xl avatar-profile"><img class="rounded-circle img-thumbnail shadow-sm" src="/profile.png"
            alt="" width="200"></div>
    </div>
    <div class="col-lg-8">
        <h4 class="mb-1"> {{$student->name}}
            </h4>
            <div class="row">
                <div class="col-6">
            <p class="mx-0 my-0">Adm:{{$student->adm_no}}</p>
            <p class="mx-0 my-0">Upi:{{$student->upi}}</p>
            <p class="mx-0 my-0">KCPE:{{$student->kcpe_marks}} - {{$student->kcpe_grade}}</p>
            <p class="mx-0 my-0">D.o.B:{{date('Y-m-D',strtotime($student->dob))}}</p>
                </div>
                 <div class="col-6">
                    <p class="mx-0 my-0">Level:{{$student->level->name}}</p>
                    <p class="mx-0 my-0">Stream:{{$student->stream->name}}</p>
                    <p class="mx-0 my-0">Guardian:
                        @foreach ($student->guardians as $guardian)
                        {{$guardian->auth->name}}
                         @endforeach</p>
                    <p class="mx-0 my-0">{{$student->description}}</p>
                 </div>
            </div>
    </div>
   </div>


@endsection