@extends('layouts.base')

@section('title', $settings->school_name)

@section('body')
<header class="fixed-top">
    <x-navbar />
</header>
<main>
    <section class="">
        <div class="container">
            <div class="min-vh-100 row align-items-center">
                <div class="col-md-7 d-none d-md-block">
                    <h1 class="display-4 fw-bold">Exam Information System for <a href="">{{ $settings->school_name }}</a></h1>
                    <p>A scalable, highly effective and complete exam managment information system tool
                        for primary schools. Ranking, Grading, Pictorial Representaion and what have you.</p>
                    <a href="{{ url(config('fortify.home')) }}" class="btn btn-lg btn-primary">Get Started</a>
                </div>

                @guest
                <form class="col-md-5" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="card shadow">
                        <div class="card-body p-3 p-md-5">
                            <div class="form-floating">
                                <input type="tel" name="phone" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="Phone Number" value="{{ old('phone') }}" autocomplete="phone"
                                    autofocus>
                                <label for="phone">Phone Number</label>
                                @error('phone')
                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                            <div class="form-floating mt-3">
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                                    autocomplete="new-password">
                                <label for="email">Password</label>
                                @error('password')
                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
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

@push('modals')
<x-modals.logout />
@endpush