@extends('layouts.base')

@section('title', 'Forgot Password')

@section('body')
    <main class="">
        <div class="container">
            <div class="min-vh-100 row justify-content-center align-items-center">
                <div class="col-12 col-sm-9 col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-text">
                                <div class="text-center">
                                    <h1 class="text-center h3">Forgot Password</h1>
                                    <hr>
                                    <h4 class="h6"><a href="{{ route('login') }}">Go back to Login</a></h4>
                                </div>
                                <x-feedback />
                                <form class="needs-validation" action="{{ route('password.email') }}" method="post">
                                    @csrf
                                    <div>
                                        <label class="fw-bold form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                        <input class="form-control form-control-sm @error('email') is-invalid @enderror" type="email"
                                            name="email" id="email" placeholder="Email Address..."
                                            value="{{ old('email') }}" />
                                        @error('email')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="mt-3">
                                        <button class="btn d-block w-100 btn-primary">Email Link</button>
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