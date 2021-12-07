<div class="card h-100">
    <div class="card-body">
        <h5>Exam Quick Actions</h5>
        <hr>
        <x-feedback />

        @can('change-exam-status')
        <div>
            <button wire:click="createScoresTable" class="btn btn-primary hstack gap-2">
                <i class="fa fa-plus"></i>
                <span>Scores Table</span>
            </button>
        </div>
        <div class="mt-3">
            <button data-bs-toggle="modal" data-bs-target="#change-status-exam-modal"
                class="btn d-block btn-primary hstack gap-2">
                <i class="fa fa-pencil-alt"></i>
                <span>Change Status</span>
            </button>
        </div>
        <x-modals.exams.change-status :name="$exam->name" :statuses="$statuses" />
        @endcan
    </div>
</div>