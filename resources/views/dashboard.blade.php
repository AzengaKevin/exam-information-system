@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')

<div class="d-flex justify-content-between">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
</div>
<hr>
<div class="row g-3">

    @can('viewAny', \App\Models\User::class)
    <a href="{{ route('users.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Users</h6>
                            <span class="fw-bold number-count">{{ \App\Models\User::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Users</h6>
                            <span class="fw-bold number-count">{{ \App\Models\User::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('viewAny', \App\Models\Teacher::class)
    <a href="{{ route('teachers.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Teachers</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Teacher::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Teachers</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Teacher::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('viewAny', \App\Models\Guardian::class)
    <a href="{{ route('guardians.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Guardians</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Guardian::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Guardians</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Guardian::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('viewAny', \App\Models\Student::class)
    <a href="{{ route('students.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Students</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Student::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Students</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Student::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan


    @can('viewAny', \App\Models\Department::class)
    <a href="{{ route('departments.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Departments</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Department::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Departments</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Department::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan


    @can('viewAny', \App\Models\Exam::class)
    <a href="{{ route('exams.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Exams</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Exam::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Exams</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Exam::count() }}</span>
                        </div>
                        <div
                            class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @if ($systemSettings->boarding_school)        
    @can('viewAny', \App\Models\Hostel::class)
    <a href="{{ route('hostels.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Hostels</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Hostel::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-bed"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Hostels</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Hostel::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-bed"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @endif

    @if ($systemSettings->school_has_streams)
    @can('viewAny', \App\Models\LevelUnit::class)
    <a href="{{ route('level-units.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Classes</h6>
                            <span class="fw-bold number-count">{{ \App\Models\LevelUnit::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-th"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Classes</h6>
                            <span class="fw-bold number-count">{{ \App\Models\LevelUnit::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-th"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @else
    @can('viewAny', \App\Models\Level::class)
    <a href="{{ route('levels.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Classes</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Level::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-th"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Classes</h6>
                            <span class="fw-bold number-count">{{ \App\Models\LevelUnit::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-th"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @endif

    @can('viewAny', \App\Models\Subject::class)
    <a href="{{ route('subjects.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Subjects</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Subject::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Subjects</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Subject::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @if ($systemSettings->school_level == 'secondary')
    @can('viewAny', \App\Models\Grading::class)
    <a href="{{ route('gradings.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Grading System</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Grading::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-equals"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Grading System</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Grading::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-equals"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @endif

    @can('viewAny', \App\Models\Responsibility::class)
    <a href="{{ route('responsibilities.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Responsibilities</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Responsibility::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
        <div class="bg-white shadow-sm card h-100 border-light">
            <div class="card-body">
                <div class="p-0 card-text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Responsibilities</h6>
                            <span class="fw-bold number-count">{{ \App\Models\Responsibility::count() }}</span>
                        </div>
                        <div
                            class="bg-primary bg-gradient w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="text-white fa fa-2x fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    
</div>
@endsection