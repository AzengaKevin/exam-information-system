@extends('layouts.dashboard')

@section('title', $hostel->slug)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{$hostel->slug}}</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-department-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Members</span>
    </button>
</div>
members
@endsection