@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
        </ol>
    </nav>
</div>
<hr>
<x-feedback />
<form action="{{ route('settings.update') }}" method="POST" class="row g-4">
    @csrf
    @method('PATCH')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5>System Settings</h5>
                <hr>
                <div class="container-fluid">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('school_name') is-invalid @enderror"
                                    name="school_name" id="school-name" placeholder="School Name"
                                    value="{{ $systemSettings->school_name }}">
                                <label for="school-name">School Name</label>
                                @error('school_name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('school_type') is-invalid @enderror"
                                    name="school_type" id="school-type" placeholder="School Type"
                                    value="{{ $systemSettings->school_type }}">
                                <label for="school-type">School Type</label>
                                @error('school_type')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('school_level') is-invalid @enderror"
                                    name="school_level" id="school-level" placeholder="School Level"
                                    value="{{ $systemSettings->school_level }}">
                                <label for="school-level">School Level</label>
                                @error('school_level')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="school_has_streams" id="school-has-streams"
                                    class="form-check-input" {{ $systemSettings->school_has_streams ? 'checked' : '' }}>
                                <label for="school-has-streams" class="form-check-label">School Has Streams</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-lg btn-primary">Update</button>
    </div>
</form>

@endsection

@push('scripts')
<script>

</script>
@endpush