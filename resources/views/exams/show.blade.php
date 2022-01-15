@extends('layouts.dashboard')

@section('title', $exam->name)

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }}</li>
        </ol>
    </nav>
</div>
<div class="row g-4 py-3">
    <div class="col-md-9">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">Basic Details</h2>
                <hr>
                <div class="row g-2 align-items-center">
                    <dl class="col-md-6">
                        <dt>Name</dt>
                        <dd>{{ $exam->name }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Short Name</dt>
                        <dd>{{ $exam->shortname }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Year</dt>
                        <dd>{{ $exam->year }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Term</dt>
                        <dd>{{ $exam->term }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Start Date</dt>
                        <dd>{{ optional($exam->start_date)->format('Y-m-d') }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>End Date</dt>
                        <dd>{{ optional($exam->end_date)->format('Y-m-d') }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>Deviation Exam</dt>
                        <dd>{{ optional($exam->deviationExam)->name ?? 'Not Provided' }}</dd>
                    </dl>

                    @if (false)
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="counts" @if($exam->counts)checked
                            @endif>
                            <label for="counts" class="form-check-label">Counts on Report Form</label>
                        </div>
                    </div>

                    @if($exam->counts)
                    <dl class="col-md-12">
                        <dt>Weight on Report Form in Percentage</dt>
                        <dd>{{ $exam->weight }}</dd>
                    </dl>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <livewire:exam-quick-actions :exam="$exam" />
    </div>

    <div class="col-md-6">
        <livewire:exam-levels :exam="$exam" />
    </div>
    <div class="col-md-6">
        <livewire:exam-subjects :exam="$exam" />
    </div>

</div>

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-exam-grades-modal', () => $('#upsert-exam-grades-modal').modal('show'))
    livewire.on('hide-upsert-exam-grades-modal', () => $('#upsert-exam-grades-modal').modal('hide'))

    livewire.on('show-delete-exam-grades-modal', () => $('#delete-exam-grades-modal').modal('show'))
    livewire.on('hide-delete-exam-grades-modal', () => $('#delete-exam-grades-modal').modal('hide'))

    livewire.on('hide-change-exam-status-modal', () => $('#change-status-exam-modal').modal('hide'))

    livewire.on('hide-update-scores-table-modal', () => $('#update-scores-table-modal').modal('hide'))
</script>
@endpush