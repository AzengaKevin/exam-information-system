@extends('layouts.base')

@section('title', 'Confirm Password')

@section('body')
<main class="">
    <div class="container">
        <div class="min-vh-100 row justify-content-center align-items-center">
            <div class="col-12 col-sm-9 col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <div class="text-center">
                                <h1 class="text-center h3">Confirm Password</h1>
                                <hr>
                            </div>
                            <x-feedback />
                            <form class="needs-validation" action="{{ route('password.confirm') }}" method="post">
                                @csrf

                                <div class="mt-3">
                                    <label class="fw-bold form-label" for="password">Password <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm @error('password') is-invalid @enderror"
                                        type="password" name="password" placeholder="Password" />
                                    @error('password')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <button class="btn d-block w-100 btn-primary">Confirm Password</button>
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