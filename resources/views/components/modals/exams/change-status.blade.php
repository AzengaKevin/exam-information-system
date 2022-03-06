@props(['name', 'statuses' => []])

<div wire:ignore.self id="change-status-exam-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="change-status-exam-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="change-status-exam-modal-title" class="modal-title">Change Exam Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Changing exam status disables and enables different exam actions. You are about to
                    change <strong>{{ $name }}</strong> status</p>
                <div class="">
                    <label for="status" class="form-label">Status</label>
                    <select wire:model.lazy="status" id="status"
                        class="form-select @error('status') is-invalid @enderror">
                        <option value="">-- Select --</option>
                        @foreach ($statuses as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" wire:click="changeExamStatus" class="btn btn-outline-info d-inline-flex gap-1">
                    <i wire:loading class="fa fa-spinner fa-spin"></i>
                    <span>Update</span>
                </button>
            </div>
        </div>
    </div>
</div>