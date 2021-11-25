@props(['departmentId' => null,'departments','subjectId'=>null])

<div wire:ignore.self id="upsert-subject-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-subject-modal-title">
    <div class="modal-dialog">
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
                <div class="mt-3">
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
                <div class="form-group mt-3">
                    <label for="">Department</label>
                    <select class="form-control" wire:model.lazy="department_id" name="" id="">
                      <option value="">--select department--</option>
                      @foreach($departments as $department)
                      <option value="{{$department->id}}">{{$department->name}}</option>
                      @endforeach
                    </select>
                  </div>
                <div class="mt-3">
                    <label for="name" class="form-label">Description</label>
                    <input type="text" wire:model.lazy="description" id="description"
                        class="form-control @error('name') is-invalid @enderror">
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