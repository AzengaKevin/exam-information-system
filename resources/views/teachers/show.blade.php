@extends('layouts.dashboard')

@section('title', $teacher->auth->name)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Teacher {{ $teacher->auth->name }} Details</h1>
</div>

<div class="row g-4 py-3">

    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">Basic Details</h2>
                <hr>
                <div class="row g-2 align-items-center">
                    <dl class="col-md-6">
                        <dt>Name</dt>
                        <dd>{{ $teacher->auth->name }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Email</dt>
                        <dd>{{ $teacher->auth->email }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Phone</dt>
                        <dd>{{ $teacher->auth->phone  ?? 'N/A'}}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>TSC Number</dt>
                        <dd>{{ $teacher->tsc_number }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5">Responsibilities</h2>

    <button data-bs-toggle="modal" data-bs-target="#assign-teacher-responsibility-modal" class="btn btn-sm btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Responsibility</span>
    </button>
                </div>
                <hr>
                <livewire:teacher-responsibilities :teacher="$teacher" type="table-sm table-bordered" />
            </div>
        </div>
    </div>
</div>

@endsection



@push('scripts')
<script>
    livewire.on('hide-assign-teacher-responsibility-modal', () => $('#assign-teacher-responsibility-modal').modal('hide'));
</script>
@endpush