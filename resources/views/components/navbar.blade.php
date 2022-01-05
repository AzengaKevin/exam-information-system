<nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient">
    <div class="container">
        <a class="navbar-brand d-inline-flex gap-2 align-items-center fs-4" href="{{ route('welcome') }}">
            @if ($generalSettings->logo)
            <img width="48" src="{{ $generalSettings->logo }}" alt="">
            @else
            <i class="fa fa-2x fa-graduation-cap"></i>
            @endif
            <div class="vr"></div>
            <span class="d-none d-md-block">{{ $systemSettings->school_name }}</span>
            <span class="d-inline d-md-none">{{ config('app.short_name') }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            </ul>
            <div class="hstack gap-3">
                @auth
                <ul class="mb-2 navbar-nav ms-md-auto mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-inline-flex align-items-center">
                                <span class="fs-4"><i class="fa fa-user"></i></span>
                                <span class="ms-2">{{ Auth::user()->name }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item text-muted" href="{{ route('home') }}">
                                    <span><i class="fa fa-home"></i></span>
                                    <span class="ms-2">Homepage</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-muted" href="{{ route('dashboard') }}">
                                    <span><i class="fa fa-tachometer-alt"></i></span>
                                    <span class="ms-2">Dashboard</span>
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
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>

                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-outline-light">Register</a>
                @endif
                @endauth
            </div>
        </div>
    </div>
</nav>