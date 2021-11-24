@extends('layouts.base')

@section('title', 'Reset Password')

@section('body')

<main>
    <div class="container">
        <div class="min-vh-100 row justify-content-center align-items-center">
            <div class="col-12 col-sm-9 col-md-7 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <div class="text-center">
                                <h1 class="text-center h3">Reset Password</h1>
                                <hr>
                                <h4 class="h6"><a href="{{ route('login') }}">Go to Login</a></h4>
                            </div>
                            <x-feedback />
                            <form class="needs-validation" action="{{ route('password.update') }}" method="post">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="container-fluid">
                                    <div class="row g-3">
                                        <div class="col-md-10">
                                            <label class="fw-bold form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                            <input class="form-control form-control-sm @error('email') is-invalid @enderror"
                                                type="email" name="email" id="email" placeholder="Email Address..."
                                                value="{{ $email ?? '' }}" />
                                            @error('email')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fw-bold form-label" for="password">Password <span class="text-danger">*</span></label>
                                            <input class="form-control form-control-sm @error('password') is-invalid @enderror"
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
                                            <input class="form-control form-control-sm @error('password') is-invalid @enderror"
                                                type="password" id="password-confirmation" name="password_confirmation"
                                                placeholder="Password..." />
                                        </div>

                                        <div class="mt-3 col-md-12">
                                            <button class="btn w-100 btn-primary btn-sm">Update Password</button>
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