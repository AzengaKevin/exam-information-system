@extends('layouts.dashboard')

@section('title', $level->name)

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @if ($systemSettings->school_has_streams)
            <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('levels.index') }}">Levels</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $level->name }}</li>
        </ol>
    </nav>
    
</div>
<hr>

<div class="row g-4 py-3">
    <div class="col-md-12">
        <livewire:level-students :level="$level" />
    </div>
</div>

@endsection