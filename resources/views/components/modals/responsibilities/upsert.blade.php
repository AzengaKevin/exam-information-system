@props([
'responsibilityId' => null,
'requirementOptions' => []
])

<div wire:ignore.self id="upsert-responsibility-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-responsibility-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($responsibilityId))
                <h5 id="upsert-responsibility-modal-title" class="modal-title">Add Responsibility</h5>
                @else
                <h5 id="upsert-responsibility-modal-title" class="modal-title">Update Responsibility</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
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
                    <label for="requirements" class="form-label">Requirement(s)</label>
                    <select wire:model.lazy="requirements" id="requirements" multiple
                        class="form-select @error('requirements') is-invalid @enderror" size="3">
                        <option value="">-- Select Requirements--</option>
                        @foreach ($requirementOptions as $item)
                        <option value="{{ $item  }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('requirements')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="name" class="form-label">Description</label>
                    <textarea wire:model.lazy="description" id="description"
                        class="form-control @error('description') is-invalid @enderror" cols="100" rows="3"></textarea>
                    @error('description')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($responsibilityId))
                <button type="submit" wire:click="createResponsibility" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateResponsibility" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>