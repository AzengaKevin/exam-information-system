<div wire:ignore.self id="assign-bulk-responsibilities-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="assign-bulk-responsibilities-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <form wire:submit.prevent="assignBulkResponsibilities" class="modal-content">
            <div class="modal-header">
                <h5 id="assign-bulk-responsibilities-modal-title" class="modal-title">Assign {{ $teacher->name }} Bulk
                    Responsibilities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="subject" class="form-label">Subject</label>
                    <select wire:model.lazy="teacher_subject_id" id="subject"
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

                @if ($systemSettings->school_has_streams)                    
                @if ($levelUnitsToShow->count())
                <div class="mt-3">
                    <label for="classes" class="form-label">Classes</label>
                    <fieldset id="classes" class="row g-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectAllClasses" id="select-all-classes"
                                    class="form-check-input">
                                <label for="select-all-classes"> {{ $selectAllClasses ? "Deselect All" : "Select All" }}</label>
                            </div>
                        </div>

                        @foreach ($levelUnitsToShow as $levelUnit)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedClasses.{{ $levelUnit->id }}"
                                    id="selected-class-{{ $loop->iteration }}" class="form-check-input">
                                <label for="selected-class-{{ $loop->iteration }}">{{ $levelUnit->alias }}</label>
                            </div>
                        </div>
                        @endforeach
                    </fieldset>
                </div>
                @endif
                @else                    
                @if ($levelsToShow->count())
                <div class="mt-3">
                    <label for="classes" class="form-label">Levels</label>
                    <fieldset id="classes" class="row g-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectAllClasses" id="select-all-classes"
                                    class="form-check-input">
                                <label for="select-all-classes"> {{ $selectAllClasses ? "Deselect All" : "Select All" }}</label>
                            </div>
                        </div>

                        @foreach ($levelsToShow as $level)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedClasses.{{ $level->id }}"
                                    id="selected-class-{{ $loop->iteration }}" class="form-check-input">
                                <label for="selected-class-{{ $loop->iteration }}">{{ $level->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </fieldset>
                </div>
                @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-outline-primary">Submit</button>
            </div>
        </form>
    </div>
</div>