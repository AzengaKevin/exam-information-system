@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row g-3">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-md-0">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Welcome</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-12">
            <figure>
                <blockquote class="blockquote">
                    <p>No man is your friend, no man is your enemy, every man is your teacher</p>
                </blockquote>
                <figcaption class="blockquote-footer">Florence Scovel Shinn</figcaption>
            </figure>
        </div>
        <a href="{{ route('dashboard') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
            <div class="bg-white shadow-sm card h-100 border-light">
                <div class="card-body">
                    <div class="p-0 card-text">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase">Dashboard</h6>
                                {{-- <span class="fw-bold number-count">{{ \App\Models\User::visible()->count() }}</span>
                                --}}
                            </div>
                            <div
                                class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <i class="text-white fa fa-2x fa-tachometer-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('profile') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
            <div class="bg-white shadow-sm card h-100 border-light">
                <div class="card-body">
                    <div class="p-0 card-text">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase">Profile</h6>
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

        <a href="{{ route('user.messages.index') }}" class="col-lg-3 col-md-6 col-sm-6 col-xs-12 text-decoration-none">
            <div class="bg-white shadow-sm card h-100 border-light">
                <div class="card-body">
                    <div class="p-0 card-text">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase">Messages</h6>
                            </div>
                            <div
                                class="bg-primary w-4 h-4 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <i class="text-white fa fa-2x fa-envelope"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection