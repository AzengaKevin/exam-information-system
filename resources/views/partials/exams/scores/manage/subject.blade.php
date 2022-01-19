<div class="container-fluid">

    <div class="d-flex flex-wrap gap-2">
        @if (false)
        <button href="#" data-bs-toggle="modal" data-bs-target="#generate-rank"
            class="btn btn-primary text-decoration-line-through">Generate Rank</button>
        @if (!empty($subject->segments))
        <button href="#" data-bs-toggle="modal" data-bs-target="#generate-totals"
            class="btn btn-primary text-decoration-line-through">Generate Total</button>
        @endif
        @if ($exam->deviationExam)            
        <button href="#" data-bs-toggle="modal" data-bs-target="#calculate-deviations"
            class="btn btn-primary d-inline-flex gap-2 align-items-center text-decoration-line-through">
            <i class="fa fa-calculator"></i>
            <span>Deviations</span>
        </button>
        @endif
        @endif
    </div>

    <hr>
    <livewire:subject-exam-scores :exam="$exam" :subject="$subject" :level="$level" :levelUnit="$levelUnit" />
</div>

@push('scripts')
<script>
    livewire.on('hide-generate-rank', () => $('#generate-rank').modal('hide'));
    livewire.on('hide-generate-totals', () => $('#generate-totals').modal('hide'));
</script>
@endpush