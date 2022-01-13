<div class="container-fluid">

    @if (false)        
    <div class="d-flex flex-wrap gap-2">
        <button href="#" data-bs-toggle="modal" data-bs-target="#generate-rank" 
            class="btn btn-primary text-decoration-line-through">Generate Rank</button>
        @if (!empty($subject->segments))            
        <button href="#" data-bs-toggle="modal" data-bs-target="#generate-totals"
            class="btn btn-primary text-decoration-line-through">Generate Total</button>
        @endif
    </div>
    @endif

    <hr>
    <livewire:subject-exam-scores :exam="$exam" :subject="$subject" :level="$level" :levelUnit="$levelUnit" />
</div>

@push('scripts')
<script>
    livewire.on('hide-generate-rank', () => $('#generate-rank').modal('hide'));
    livewire.on('hide-generate-totals', () => $('#generate-totals').modal('hide'));
</script>
@endpush