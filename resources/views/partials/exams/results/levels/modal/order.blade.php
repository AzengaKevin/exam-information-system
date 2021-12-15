<div wire:ignore.self id="order-level-{{ $level->id }}-exam-results-modal" class="modal fade" tabindex="-1"
    data-bs-backdrop="static" aria-labelledby="order-level-{{ $level->id }}-exam-results-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="order-level-{{ $level->id }}-exam-results-modal-title" class="modal-title">Order
                    {{ $level->name }} Exam Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="level-unit" class="form-label">Order By</label>
                    <select wire:model.lazy="orderBy" id="level-unit"
                        class="form-select @error('orderBy') is-invalid @enderror">
                        <option value="">-- Select Level Unit --</option>
                        <option value="admno">Admission Number</option>
                        <option value="name">Name</option>
                        <option value="level_position">Rank</option>
                    </select>
                    @error('orderBy')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>