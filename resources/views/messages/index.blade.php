@extends('layouts.dashboard')

@section('title', 'Messages')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Messages</li>
        </ol>
    </nav>
    <div class="hstack gap-2">
        <button class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Message</span>
        </button>
    </div>
</div>
<hr>

<livewire:user-messages />

@endsection

@push('scripts')
<script></script>
@endpush