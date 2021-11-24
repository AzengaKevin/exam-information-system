@extends('layouts.base')

@section('body')
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fs-4" href="#">
            <span class="d-none d-md-block">{{ config('app.name') }}</span>
            <span class="d-inline d-md-none">{{ config('app.short_name') }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
@endsection