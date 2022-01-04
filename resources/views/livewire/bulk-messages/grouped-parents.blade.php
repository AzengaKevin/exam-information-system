<div wire:ignore.self id="grouped-parents-bulk-sms-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="grouped-parents-bulk-sms-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="grouped-parents-bulk-sms-modal-title" class="modal-title">
                    <span>Send Bulk Messages</span>
                    <small class="text-sm text-muted">By Grouped Parents</small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="group-by" class="form-label fw-bold">Group By</label>
                    <fieldset id="group-by" class="d-flex flex-wrap gap-2">
                        <div class="form-check">
                            <input type="radio" name="groupBy" id="all-parents" class="form-check-input" value="all">
                            <label for="all-parents" class="form-check-label">All Parents</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="groupBy" id="level-parents" class="form-check-input" value="levels">
                            <label for="level-parents" class="form-check-label">Levels</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="groupBy" id="stream-parents" class="form-check-input" value="streams">
                            <label for="stream-parents" class="form-check-label">Streams</label>
                        </div>
                    </fieldset>
                </div>
                <div class="mt-3">
                    <label for="levels" class="form-label fw-bold">Levels</label>
                    <fieldset class="row g-2" id="levels">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="levels" id="level-1" class="form-check-input" value="true">
                                <label for="level-1" class="form-check-label">Grade One</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="levels" id="level-2" class="form-check-input" value="true">
                                <label for="level-2" class="form-check-label">Grade Two</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="levels" id="level-3" class="form-check-input" value="true">
                                <label for="level-3" class="form-check-label">Grade Three</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="levels" id="level-4" class="form-check-input" value="true">
                                <label for="level-4" class="form-check-label">Grade Four</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>