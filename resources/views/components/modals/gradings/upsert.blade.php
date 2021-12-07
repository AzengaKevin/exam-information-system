@props(['gradingId' => null, 'grades' => [], 'values' => []])

<div wire:ignore.self id="upsert-grading-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-grading-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($gradingId))
                <h5 id="upsert-grading-modal-title" class="modal-title">Add Grading</h5>
                @else
                <h5 id="upsert-grading-modal-title" class="modal-title">Update Grading</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">

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

                    <fieldset class="mt-3">
                        <label for="values" class="form-label">Grading Values</label>
                        <div id="values" class="row g-3">
                            @foreach ($values as $index => $value)
                            <div class="col-12">
                                <div class="input-group">
                                    <select wire:model.lazy="values.{{ $index }}.grade" id="values-grade-{{ $index }}"
                                        class="form-select @error('values.{{ $index }}.grade') is-invalid @enderror">
                                        <option value="">-- Select --</option>
                                        @foreach ($grades as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" wire:model.lazy="values.{{ $index }}.points" min="0" max="12"
                                        id="values-points-{{ $index }}" placeholder="Points"
                                        class="form-control @error('values.{{ $index }}.points') is-invalid @enderror">
                                    <input type="number" wire:model.lazy="values.{{ $index }}.min" min="0" max="100"
                                        id="values-min-{{ $index }}" placeholder="Min"
                                        class="form-control @error('values.{{ $index }}.min') is-invalid @enderror">
                                    <input type="number" wire:model.lazy="values.{{ $index }}.max" min="0" max="100"
                                        id="values-max-{{ $index }}" placeholder="Max"
                                        class="form-control @error('values.{{ $index }}.max') is-invalid @enderror">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($gradingId))
                <button type="submit" wire:click="addGrading" class="btn btn-outline-primary">Create</button>
                @else
                <button type="submit" wire:click="updateGrading" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>