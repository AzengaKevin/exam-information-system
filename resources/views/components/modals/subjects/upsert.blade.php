@props(['departmentId' => null,'departments','subjectId'=>null, 'segments' => [], 'levels' => []])

<div wire:ignore.self id="upsert-subject-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-subject-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($departmentId))
                <h5 id="upsert-subject-modal-title" class="modal-title">Add Subject</h5>
                @else
                <h5 id="upsert-subject-modal-title" class="modal-title">Update Subject</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input type="text" wire:model.lazy="name" id="name"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="shortname" class="form-label">Shortname</label>
                    <input type="text" wire:model.lazy="shortname" id="shortname"
                        class="form-control @error('shortname') is-invalid @enderror">
                    @error('shortname')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="name" class="form-label">Subject Code</label>
                    <input type="text" wire:model.lazy="subject_code" id="name"
                        class="form-control @error('subject_code') is-invalid @enderror">
                    @error('subject_code')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-control" wire:model.lazy="department_id" id="department">
                        <option value="">--select department--</option>
                        @foreach($departments as $department)
                        <option value="{{$department->id}}">{{$department->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-3">
                    <div class="form-check">
                        <input type="checkbox" wire:model="optional" id="optional-subject-check" class="form-check-input">
                        <label for="optional-subject-check" class="form-check-label">Optional Subject</label>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="segments" class="form-label fw-bold">Segments(Optional)</label>
                    <fieldset id="segments" class="d-flex flex-column gap-3">
                        @foreach ($segments as $segment)
                        <div class="input-group">
                            <select wire:model="segments.{{ $loop->index }}.level_id" 
                                class="form-select @error("segments.{{ $loop->index }}.level_id") is-invalid @enderror">
                                <option value="">Level...</option>
                                @foreach ($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" wire:model.lazy="segments.{{ $loop->index }}.key" class="form-control"
                                placeholder="Field" aria-label="Field">
                            <input type="number" wire:model.lazy="segments.{{ $loop->index }}.value"
                                class="form-control" placeholder="Out Of" aria-label="Out Of">
                            <button type="button" wire:click="removeSegmentFields({{ $loop->index }})"
                                class="btn btn-outline-danger"><i class="fa fa-minus"></i></button>
                        </div>
                        @endforeach
                        <div>
                            <button type="button" wire:click="addSegmentFields"
                                class="btn btn-sm btn-outline-primary d-inline-flex gap-2 align-items-center">
                                <i class="fa fa-plus"></i>
                                <span>Fields</span>
                            </button>
                        </div>
                    </fieldset>
                </div>
                <div class="mt-3">
                    <label for="name" class="form-label">Description</label>
                    <textarea wire:model.lazy="description" id="description" cols="100" rows="4"
                        class="form-control @error('description') is-invalid @enderror"></textarea>
                    @error('description')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($subjectId))
                <button type="submit" wire:click="createSubject" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateSubject" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>