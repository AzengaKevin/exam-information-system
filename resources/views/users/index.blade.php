@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">Users</h1>
</div>
<hr>

<livewire:users />

@endsection