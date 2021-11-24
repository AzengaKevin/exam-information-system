@extends('layouts.base')

@section('body')
<header class="fixed-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient">
        <div class="container">
            <a class="navbar-brand fs-4" href="{{ route('welcome') }}">
                <span class="d-none d-md-block">{{ config('app.name') }}</span>
                <span class="d-inline d-md-none">{{ config('app.short_name') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                </ul>
                <div class="hstack gap-3">
                    <a href="#" class="btn btn-outline-light">Login</a>
                    <a href="#" class="btn btn-outline-light">Register</a>
                </div>
            </div>
        </div>
    </nav>
</header>
<main>
    <section class="">
        <div class="container">
            <div class="min-vh-100 row align-items-center">
                <div class="col-md-7 d-none d-md-block">
                    <h1 class="display-3 fw-bold">Effectively Manage School Exams</h1>
                    <p>A scalable, highly effective and complete exam managment information system tool
                        for primary schools. Ranking, Grading, Pictorial Representaion and what have you.</p>
                    <a href="#" class="btn btn-lg btn-primary">Get Started</a>
                </div>
                
                @guest
                <form class="col-md-5" action="">
                    <div class="card shadow">

                        <div class="card-body p-3 p-md-5">
                            <div class="form-floating">
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="Email Address">
                                <label for="email">Email Address</label>
                            </div>
                            <div class="form-floating mt-3">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Password">
                                <label for="email">Password</label>
                            </div>
                            <div class="mt-3">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                    <label class="form-check-label" for="remember">Remember Me</label>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-outline-primary">Login</button>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="col-md-5">
                    <img class="w-100" src="{{ asset('images/data-processing.svg') }}" alt="Data Processing SVG">
                </div>
                @endguest
            </div>
        </div>
    </section>
</main>
@endsection