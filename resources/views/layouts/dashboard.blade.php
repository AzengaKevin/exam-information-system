@extends('layouts.base')

@section('metas')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('body')
<div id="wrapper" class="vh-100 row g-0 bg-light">
    <div class="h-100 d-none d-md-block col-md-3 col-lg-2 bg-primary overflow-auto">
        <div class="p-3 text-white d-flex align-items-center">
            <div><i class="fa fa-3x fa-user"></i></div>
            <div class="ms-3">
                <h5>{{ Auth()->user()->name }}</h5>
                <small class="text-white-50">Dashboard</small>
            </div>
        </div>
        <hr class="bg-white">
        <ul class="nav flex-column">
            <li class="nav-item btn-dashboard">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'text-white' : 'text-white-50' }}" href="{{ route('dashboard') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-tachometer-alt"></i></span>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('roles.index') ? 'text-white':'text-white-50'}}" href="{{route('roles.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-users-cog"></i></span>
                        <span>Roles</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('permissions.index') ? 'text-white':'text-white-50'}}" href="{{route('permissions.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-users-cog"></i></span>
                        <span>Permissions</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.index') ? 'text-white' : 'text-white-50' }}" href="{{ route('users.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-users"></i></span>
                        <span>Users</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('levels.index') ? 'text-white' : 'text-white-50' }}" href="{{route('levels.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-layer-group"></i></span>
                        <span>Levels</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('streams.index') ? 'text-white':'text-white-50' }}" href="{{route('streams.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-layer-group"></i></span>
                        <span>Streams</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('departments.index') ? 'text-white':'text-white-50' }}" href="{{route('departments.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-layer-group"></i></span>
                        <span>Departments</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('subjects.index') ? 'text-white':'text-white-50' }}" href="{{route('subjects.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-graduate"></i></span>
                        <span>Subjects</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('students.index') ? 'text-white':'text-white-50' }}" href="{{ route('students.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-graduate"></i></span>
                        <span>Students</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teachers.index') ? 'text-white' : 'text-white-50' }}" href="{{ route('teachers.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-tie"></i></span>
                        <span>Teachers</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('guardians.index') ? 'text-white' : 'text-white-50' }}" href="{{ route('guardians.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-tie"></i></span>
                        <span>Guardians</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('exams.index') ? 'text-white' : 'text-white-50' }}" href="{{ route('exams.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-file-word" aria-hidden="true"></i></span>
                        <span>Exams</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('responsibilities.index') ? 'text-white' : 'text-white-50' }}" href="{{ route('responsibilities.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-file-word" aria-hidden="true"></i></span>
                        <span>Responsibilities</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('level-units.index') ? 'text-white' : 'text-white-50' }}" href="{{ route('level-units.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-file-pdf" aria-hidden="true"></i></span>
                        <span>Level Units</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="vh-100 col-md-9 col-lg-10 d-flex flex-column">
        <nav class="px-2 bg-white shadow-sm navbar navbar-expand-md navbar-light px-md-3">
            <a class="navbar-brand" href="{{ route('welcome') }}">{{ config('app.short_name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="mb-2 navbar-nav me-auto mb-lg-0 d-md-none">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Roles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Users</a>
                    </li>
                </ul>
                <ul class="mb-2 navbar-nav ms-auto mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <div class="d-inline-flex align-items-center">
                                <span class="fs-4"><i class="fa fa-user"></i></span>
                                <span class="ms-2">{{ Auth::user()->name }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item text-muted" href="{{ route('home') }}">
                                    <span><i class="fa fa-home"></i></span>
                                    <span class="ms-2">Home Page</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-muted" href="#">
                                    <span><i class="fa fa-user"></i></span>
                                    <span class="ms-2">My Profile</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-muted" href="#" data-bs-toggle="modal"
                                    data-bs-target="#logout-modal">
                                    <span><i class="fa fa-sign-out-alt"></i></span>
                                    <span class="ms-2">Logout</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="p-3 overflow-auto flex-grow-1">
            @yield('content')
        </div>
    </div>
</div>
@endsection

@push('modals')
<x-modals.logout />
@endpush