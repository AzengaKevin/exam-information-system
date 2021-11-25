@extends('layouts.dashboard')

@section('title', 'Administrator Dashboard')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Dashboard</h1>
</div>
<hr>
<div class="row g-3">
    <a href="{{ route('users.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Users</h6>
                            <span class="fw-bold number-count">{{ \App\Models\User::count() }}</span>
                        </div>
                        <div class="bg-success rounded-circle p-3">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <a href="{{ route('teachers.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Teachers</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Teacher::count() }}</span>
                        </div>
                        <div class="bg-primary rounded-circle p-3">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a href="{{ route('guardians.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Guardians</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Guardian::count() }}</span>
                        </div>
                        <div class="bg-info rounded-circle p-3">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>

</div>


@endsection