@extends('layouts.dashboard')

@section('title', $exam->name)

@section('content')

<div class="d-flex justify-content-between align-items-center">
    <h1 class="h4 fw-bold text-muted">{{ $exam->name }}</h1>

    <div class="btn-group">
        <button data-bs-toggle="modal" data-bs-target="#upsert-exam-grades-modal"
            class="btn btn-outline-primary hstack gap-2 align-items-center">
            <i class="fa fa-plus"></i>
            <span>Grade</span>
        </button>

        @can('access-upload-scores-page')
        <a href="{{ route('exams.scores.index', $exam) }}" class="btn btn-outline-primary gap-2 align-items-center">
            <i class="fa fa-upload"></i>
            <span class="d-none d-md-inline">Scores</span>
        </a>
        @endcan
        <a href="{{ route('exams.results.index', $exam) }}" class="btn btn-outline-primary gap-2 align-items-center">
            <i class="fa fa-table"></i>
            <span class="d-none d-md-inline">Results</span>
        </a>
        <a href="{{ route('exams.analysis.index', $exam) }}" class="btn btn-outline-primary gap-2 align-items-center">
            <i class="fa fa-poll"></i>
            <span class="d-none d-md-inline">Analysis</span>
        </a>
    </div>
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
                        <dd>{{ $exam->start_date }}</dd>
                    </dl>
                    <dl class="col-md-6">
                        <dt>End Date</dt>
                        <dd>{{ $exam->end_date }}</dd>
                    </dl>
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
    <div class="col-md-12">
        @livewire('exam-grades',['exam'=>$exam])
    </div>

</div>

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-exam-grades-modal', () => $('#upsert-exam-grades-modal').modal('show'))
    livewire.on('hide-upsert-exam-grades-modal', () => $('#upsert-exam-grades-modal').modal('hide'))
    livewire.on('show-delete-exam-grades-modal', () => $('#delete-exam-grades-modal').modal('show'))
    livewire.on('hide-delete-exam-grades-modal', () => $('#delete-exam-grades-modal').modal('hide'))
</script>
@endpush