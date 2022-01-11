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
<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="row g-4">
    @csrf
    @method('PATCH')

    @if ($user->email == 'azenga.kevin7@gmail.com')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5>System Settings</h5>
                <hr>
                <div class="container-fluid">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('system.school_name') is-invalid @enderror"
                                    name="system[school_name]" id="school-name" placeholder="School Name"
                                    value="{{ old('system.school_name') ?? $systemSettings->school_name }}">
                                <label for="school-name">School Name</label>
                                @error('system.school_name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('system.school_type') is-invalid @enderror"
                                    name="system[school_type]" id="school-type" placeholder="School Type"
                                    value="{{ old('system.school_type') ?? $systemSettings->school_type }}">
                                <label for="school-type">School Type</label>
                                @error('system.school_type')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('system.school_level') is-invalid @enderror"
                                    name="system[school_level]" id="school-level" placeholder="School Level"
                                    value="{{ old('system.school_level') ?? $systemSettings->school_level }}">
                                <label for="school-level">School Level</label>
                                @error('system.school_level')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="system[school_has_streams]" id="school-has-streams"
                                    class="form-check-input"
                                    {{ (old('system.school_has_streams') ?? $systemSettings->school_has_streams) ? 'checked' : '' }}>
                                <label for="school-has-streams" class="form-check-label">School Has Streams</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="system[boarding_school]" id="school-is-a-bording-one"
                                    class="form-check-input"
                                    {{ (old('system.boarding_school') ?? $systemSettings->boarding_school) ? 'checked' : '' }}>
                                <label for="school-is-a-bording-one" class="form-check-label">Is a Boarding
                                    School</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5>General Settings</h5>
                <hr>
                <div class="container-fluid">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="logo" class="form-label fw-bold">School Badge | Logo</label>
                            <div id="logo" class="d-flex align-items-start align-items-md-center flex-wrap gap-2">
                                <div>
                                    @if ($generalSettings->logo)
                                    <img src="{{ $generalSettings->logo }}" width="72" height="auto"
                                        alt="{{ $systemSettings->school_name }}">
                                    @else
                                    <i class="fa fa-3x fa-graduation-cap"></i>
                                    @endif
                                </div>
                                <div>
                                    <input type="file" name="raw[logo]" aria-describedby="logo-file-desc" id="logo-file"
                                        class="form-control">
                                    <div class="form-text">Change School Badge | Logo</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('general.school_website') is-invalid @enderror"
                                    name="general[school_website]" id="school-website" placeholder="School Website"
                                    value="{{ old('general.school_website') ?? $generalSettings->school_website }}">
                                <label for="school-website">School Website</label>
                                @error('general.school_website')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('general.school_address') is-invalid @enderror"
                                    name="general[school_address]" id="school-address" placeholder="School Address"
                                    value="{{ $generalSettings->school_address }}">
                                <label for="school-address">School Address</label>
                                @error('general.school_address')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('general.school_telephone_number') is-invalid @enderror"
                                    name="general[school_telephone_number]" id="school-telephone-number"
                                    placeholder="School Telephone Number"
                                    value="{{ $generalSettings->school_telephone_number }}">
                                <label for="school-telephone-number">School Telephone Number</label>
                                @error('general.school_telephone_number')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('general.school_email_address') is-invalid @enderror"
                                    name="general[school_email_address]" id="school-email-address"
                                    placeholder="School Email Address"
                                    value="{{ $generalSettings->school_email_address }}">
                                <label for="school-email-address">School Email Address</label>
                                @error('general.school_email_address')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number"
                                    class="form-control @error('general.current_academic_year') is-invalid @enderror"
                                    name="general[current_academic_year]" id="current-academic-year"
                                    placeholder="Current Academic Year"
                                    value="{{ old('general.current_academic_year') ?? $generalSettings->current_academic_year }}">
                                <label for="current-academic-year">Current Academic Year</label>
                                @error('general.current_academic_year')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('general.current_term') is-invalid @enderror"
                                    name="general[current_term]" id="current-term" placeholder="Current Term"
                                    value="{{ old('general.current_term') ?? $generalSettings->current_term }}">
                                <label for="current-term">Current Term</label>
                                @error('general.current_term')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
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