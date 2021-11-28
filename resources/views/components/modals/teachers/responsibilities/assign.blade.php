@props([
'levels' => [],
'levelUnits' => [],
'subjects' => [],
'departments' => [],
'responsibilityOptions' => []
])

<div wire:ignore.self id="assign-teacher-responsibility-modal" class="modal fade" tabindex="-1"
    data-bs-backdrop="static" aria-labelledby="assign-teacher-responsibility-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="assign-teacher-responsibility-modal-title" class="modal-title">Assign Responsibility</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="responsibility" class="form-label">Responsibility</label>
                    <select wire:model.lazy="responsibility_id" id="responsibility"
                        class="form-select @error('responsibility_id') is-invalid @enderror">
                        <option value="">-- Select Responsibility --</option>
                        @foreach ($responsibilityOptions as $responsibility)
                        <option value="{{ $responsibility->id }}">{{ $responsibility->name }}</option>
                        @endforeach
                    </select>
                    @error('responsibility_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="department" class="form-label">Department</label>
                    <select wire:model.lazy="department_id" id="department"
                        class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">-- Select Department --</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
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

                <div class="mt-3">
                    <label for="subject" class="form-label">Subject</label>
                    <select wire:model.lazy="subject_id" id="subject"
                        class="form-select @error('subject_id') is-invalid @enderror">
                        <option value="">-- Select Subject --</option>
                        @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="level-unit" class="form-label">Level Unit</label>
                    <select wire:model.lazy="level_unit_id" id="level-unit"
                        class="form-select @error('level_unit_id') is-invalid @enderror">
                        <option value="">-- Select Level Unit --</option>
                        @foreach ($levelUnits as $levelUnit)
                        <option value="{{ $levelUnit->id }}">{{ $levelUnit->name }}</option>
                        @endforeach
                    </select>
                    @error('level_unit_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="type" wire:click="assignResponsibility" class="btn btn-outline-primary">Submit</button>
            </div>
        </div>
    </div>
</div>