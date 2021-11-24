@extends('layouts.base')

@section('body')
<header class="sticky-top">
    <x-navbar />
</header>
<main class="py-4">
    @yield('content')
</main>
<footer>

</footer>
@endsection

@push('modals')
<x-modals.logout />
@endpush