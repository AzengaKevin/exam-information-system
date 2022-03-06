<div wire:ignore.self id="export-student-spreadsheet-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="export-student-spreadsheet-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="export-student-spreadsheet-modal-title" class="modal-title">Export Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead">Are you sure you want to export students into a spreadsheet</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-outline-primary d-inline-flex gap-1">
                    <i  wire:loading class="fa fa-spinner fa-spin"></i>
                    <span>Download</span>
                </button>
            </div>
        </div>
    </div>
</div>