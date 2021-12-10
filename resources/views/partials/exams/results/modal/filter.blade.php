<div wire:ignore.self id="filter-exam-results" class="modal fade" tabindex="-1"
    data-bs-backdrop="static" aria-labelledby="filter-exam-results-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="filter-exam-results-title" class="modal-title">Filter Exam Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
        
                        <div class="col-md-6">
                            <label for="level" class="form-label">Level</label>
                            <select wire:model.lazy="level_id" id="level"
                                class="form-select @error('level_id') is-invalid @enderror">
                                <option value="">-- Select Level --</option>
                                @foreach ($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('level_id')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
        
                        <div class="col-md-6">
                            <label for="level-unit" class="form-label">Level Unit</label>
                            <select wire:model.lazy="level_unit_id" id="level-unit"
                                class="form-select @error('level_unit_id') is-invalid @enderror">
                                <option value="">-- Select Level Unit --</option>
                                @foreach ($levelUnits as $levelUnit)
                                <option value="{{ $levelUnit->id }}">{{ $levelUnit->alias }} </option>
                                @endforeach
                            </select>
                            @error('level_unit_id')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" wire:model.lazy="name" id="name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="adm-no" class="form-label">Admission Number</label>
                            <input type="text" wire:model="admno" id="adm-no"
                                class="form-control @error('admno') is-invalid @enderror">
                            @error('admno')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>               
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>