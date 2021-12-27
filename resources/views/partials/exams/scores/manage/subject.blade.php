<div class="container-fluid">

    <div class="d-flex">
        <button href="#" data-bs-toggle="modal" data-bs-target="#generate-rank" class="btn btn-primary">Generate
            Rank</button>
    </div>

    <hr>
    <livewire:subject-exam-scores :exam="$exam" :subject="$subject" :level="$level" :levelUnit="$levelUnit" />
</div>

@push('scripts')
<script>
    livewire.on('hide-generate-rank', () => $('#generate-rank').modal('hide'));
</script>
@endpush