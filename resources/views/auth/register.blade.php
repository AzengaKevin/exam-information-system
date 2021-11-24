@extends('layouts.base')

@section('title', 'Join Us')

@section('body')

<main>
    <div class="container">
        <div class="min-vh-100 row justify-content-center align-items-center">
            <div class="col-12 col-sm-9 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <div class="text-center">
                                <h1 class="text-center h3">Register</h1>
                                <hr>
                                <h4 class="h6">Already Have an Account ? <a href="{{ route('login') }}">Login</a></h4>
                            </div>

                            <form class="needs-validation" action="{{ route('register') }}" method="post">
                                @csrf

                                <div class="container-fluid">
                                    <div class="row g-3">

                                        <div>
                                            <label class="fw-bold form-label" for="name">Name <span class="text-danger">*</span></label>
                                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                                name="name" id="name" placeholder="Name..." value="{{ old('name') }}" />
                                            @error('name')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-12">
                                            <label class="fw-bold form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                            <input class="form-control @error('email') is-invalid @enderror"
                                                type="email" name="email" id="email" placeholder="Email Address..."
                                                value="{{ old('email') }}" />
                                            @error('email')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fw-bold form-label" for="password">Password <span class="text-danger">*</span></label>
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" id="password" name="password"
                                                placeholder="Password..." />
                                            @error('password')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fw-bold form-label" for="password-confirmation">Confirm
                                                Password <span class="text-danger">*</span></label>
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" id="password-confirmation" name="password_confirmation"
                                                placeholder="Password..." />
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="terms-and-condition" id="terms-and-condition">
                                                
                                                <label for="terms-and-condition" class="form-check-label">Agree to <a href="#"> Terms & Condition </a><span class="text-danger">*</span></label>
                                            </div>
                                        </div>

                                        <div class="mt-3 col-md-12">
                                            <button class="btn w-100 btn-primary">Sign Up</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection