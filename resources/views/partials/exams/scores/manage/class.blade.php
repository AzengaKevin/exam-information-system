<div class="container-fluid">
    <div class="d-flex gap-2 align-items-md-center flex-wrap">
        <a href="#" data-bs-toggle="modal" data-bs-target="#generate-scores-aggregates-modal" role="button"
            class="btn btn-primary d-inline-flex gap-2 align-items-center">
            <i class="fa fa-calculator"></i>
            <span>Aggregates</span>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#publish-class-scores-modal" role="button"
            class="btn btn-primary d-inline-flex gap-2 align-items-center">
            <i class="fa fa-upload"></i>
            <span>Publish</span>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#rank-class-modal" role="button"
            class="btn btn-primary d-inline-flex gap-2 align-items-center">
            <i class="fa fa-sort-amount-down"></i>
            <span>Rank</span>
        </a>
    </div>
    <hr>
    <livewire:level-unit-exam-scores :exam="$exam" :levelUnit="$levelUnit" />

</div>