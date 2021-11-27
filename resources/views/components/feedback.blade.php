@env('local')
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endenv

@if (session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('status') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif