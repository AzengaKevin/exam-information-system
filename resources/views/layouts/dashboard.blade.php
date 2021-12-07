@extends('layouts.base')

@section('metas')
<meta name="robots" content="noindex, nofollow">
@endsection

@section('body')
<div id="wrapper" class="vh-100 row g-0 bg-light">
    <div id="sidebar" class="h-100 d-none d-md-block col-md-3 col-lg-2 bg-primary overflow-y-auto">
        <div class="d-flex">
            <div class="p-3 text-white d-flex align-items-center flex-grow-1">
                <div><i class="fa fa-3x fa-user"></i></div>
                <div class="ms-3">
                    <h5>{{ Auth()->user()->name }}</h5>
                    <small class="text-white-50">Dashboard</small>
                </div>
            </div>
            <div>
                <button id="hide-sidebar" class="btn d-none text-white fs-4">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <hr class="bg-white">
        <ul class="nav flex-column">
            <li class="nav-item btn-dashboard">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('dashboard') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-tachometer-alt"></i></span>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>
            @can('viewAny', \App\Models\Role::class)
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('roles.index') ? 'text-white':'text-white-50'}}"
                    href="{{route('roles.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-users-cog"></i></span>
                        <span>Authorization</span>
                    </div>
                </a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('users.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-users"></i></span>
                        <span>Users</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('departments.index') ? 'text-white':'text-white-50' }}"
                    href="{{route('departments.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-th"></i></span>
                        <span>Departments</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('subjects.index') ? 'text-white':'text-white-50' }}"
                    href="{{route('subjects.index')}}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-book"></i></span>
                        <span>Subjects</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('students.index') ? 'text-white':'text-white-50' }}"
                    href="{{ route('students.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-graduate"></i></span>
                        <span>Students</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teachers.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('teachers.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-tie"></i></span>
                        <span>Teachers</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('guardians.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('guardians.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fas fa-user-tie"></i></span>
                        <span>Guardians</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('exams.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('exams.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-file-alt" aria-hidden="true"></i></span>
                        <span>Exams</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('gradings.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('gradings.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-file-alt" aria-hidden="true"></i></span>
                        <span>Grading System</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('responsibilities.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('responsibilities.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-tasks" aria-hidden="true"></i></span>
                        <span>Responsibilities</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('level-units.index') ? 'text-white' : 'text-white-50' }}"
                    href="{{ route('level-units.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-th" aria-hidden="true"></i></span>
                        <span>Classes</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('hostels.index') ? 'text-white':'text-white-50' }}"
                    href="{{ route('hostels.index') }}">
                    <div class="hstack gap-3">
                        <span><i class="fa fa-bed" aria-hidden="true"></i></span>
                        <span>Hostels</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div id="content" class="vh-100 col-md-9 col-lg-10 d-flex flex-column">
        <nav class="px-2 bg-white shadow-sm d-flex align-items-center justify-content-between px-md-3 py-1">
            <div>
                <button id="sidebar-toggler" class="btn">
                    <i class="fa fa-bars"></i>
                </button>
            </div>
            <ul class="list-unstyled my-0">
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
        </nav>
        <div class="p-3 overflow-y-auto flex-grow-1">
            @yield('content')
        </div>
    </div>
</div>
@endsection

@push('modals')
<x-modals.logout />
@endpush

@push('scripts')
<script>
    $("#sidebar-toggler").click(function(){

        const width = $(window).width();

        if(width < 768){

            $("#sidebar").toggleClass("d-none");
            $("#content").toggleClass("d-none");
            
            $("#hide-sidebar").toggleClass('d-none');

        }else{

            $("#sidebar").toggleClass('d-md-block');
            $("#content").toggleClass('col-md-9 col-lg-10').toggleClass('col-12');

        }
    });

    $("#hide-sidebar").click(function(){

        const width = $(window).width();

        if(width < 768){
            $("#sidebar").toggleClass("d-none");
            $("#content").toggleClass("d-none");

            $("#hide-sidebar").toggleClass('d-none');
        }

    });

</script>
@endpush