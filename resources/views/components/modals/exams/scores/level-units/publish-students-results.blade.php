@props(['levelUnit'])

<div wire:ignore.self id="publish-class-students-results-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="publish-class-students-results-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="publish-class-students-results-modal-title" class="modal-title">Publish {{ $levelUnit->alias }} Student
                    Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure want to publish student results for <strong>{{ $levelUnit->alias }}</strong>? You can
                    republish student results until the exam is published</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="publishStudentResults"
                    class="btn btn-outline-primary">Proceed</button>
            </div>
        </div>
    </div>
</div>