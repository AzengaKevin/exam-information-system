<div wire:ignore.self id="import-guardians-spreadsheet-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="import-guardians-spreadsheet-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="import-guardians-spreadsheet-modal-title" class="modal-title">Import Guardians</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You can import students guardians by filling their details in a file of <button type="button"
                        wire:click="downloadStudentsGuardiansFile" class="btn btn-link">this format</button>, and
                    re-uploading the file in the form below</p>
                <div class="mt-3">
                    <label for="file" class="form-label">Students Guardians Excel File</label>
                    <input type="file" wire:model="guardiansImportFile" id="file"
                        class="form-control @error('guardiansImportFile') is-invalid @enderror">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="importStudentsGuardians" class="btn btn-outline-primary">Upload</button>
            </div>
        </div>
    </div>
</div>