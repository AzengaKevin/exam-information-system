<nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient">
    <div class="container">
        <a class="navbar-brand fs-4" href="{{ route('welcome') }}">
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
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
            </ul>
            <div class="hstack gap-3">
                @auth
                <button type="button" data-bs-toggle="modal" data-bs-target="#logout-modal"
                    class="btn btn-outline-danger">Logout</button>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
                <a href="#" class="btn btn-outline-light">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>