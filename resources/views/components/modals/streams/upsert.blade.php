@props(['streamId' => null, 'optionalSubjects' => []])

<div wire:ignore.self id="upsert-stream-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-stream-modal-title">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($streamId))
                <h5 id="upsert-stream-modal-title" class="modal-title">Add Stream</h5>
                @else
                <h5 id="upsert-stream-modal-title" class="modal-title">Update Stream</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-9">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" wire:model.lazy="name" id="name"
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="name" class="form-label">Alias</label>
                        <input type="text" wire:model.lazy="alias" id="alias"
                            class="form-control @error('name') is-invalid @enderror">
                        @error('alias')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="subjects" class="form-label fw-bold">Optional Subjects</label>
                        <fieldset class="row g-2">
                            @foreach ($optionalSubjects as $subject)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" wire:model="selectedOptionalSubjects.{{ $subject->id }}"
                                        id="optional-subject-{{ $loop->index }}" value="true" class="form-check-input">
                                    <label for="optional-subject-{{ $loop->index }}"
                                        class="form-check-label">{{ $subject->name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </fieldset>
                    </div>
                    <div class="col-md-12">
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
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($streamId))
                <button type="submit" wire:click="createStream" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateStream" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>