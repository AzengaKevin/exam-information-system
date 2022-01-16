<div class="container-fluid">
    <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#publish-class-scores-modal" role="button"
            class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="fa fa-upload"></i>
            <span>Publish Scores</span>
        </a>
        @if (!$systemSettings->school_has_streams)
        <a href="#" data-bs-toggle="modal" data-bs-target="#generate-aggregates-modal" role="button"
            class="btn btn-primary d-inline-flex align-items-center gap-2 text-decoration-line-through">
            <i class="fa fa-upload"></i>
            <span>Generate Aggregates.</span>
        </a>
        @endif
        <a href="#" data-bs-toggle="modal" data-bs-target="#rank-class-modal" role="button"
            class="btn btn-primary d-inline-flex align-items-center gap-2 text-decoration-line-through">
            <i class="fa fa-sort-amount-down"></i>
            <span>Rank</span>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#publish-level-grade-dist-modal" role="button"
            class="btn btn-primary d-inline-flex align-items-center gap-2 text-decoration-line-through">
            <i class="fa fa-upload"></i>
            <span>Publish Grade Dist.</span>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#publish-subjects-performance-modal" role="button"
            class="btn btn-primary d-inline-flex align-items-center gap-2 text-decoration-line-through">
            <i class="fa fa-upload"></i>
            <span>Publish Subject Performance.</span>
        </a>
        @if (!$systemSettings->school_has_streams)            
        <a href="#" data-bs-toggle="modal" data-bs-target="#publish-students-results-modal" role="button"
            class="btn btn-primary d-inline-flex align-items-center gap-2 text-decoration-line-through">
            <i class="fa fa-upload"></i>
            <span>Publish Student Results</span>
        </a>
        @endif
    </div>
    <livewire:level-exam-scores :exam="$exam" :level="$level" />
</div>

@push('scripts')
<script>
    livewire.on('hide-generate-aggregates-modal', () => $('#generate-aggregates-modal').modal('hide'))
</script>
@endpush