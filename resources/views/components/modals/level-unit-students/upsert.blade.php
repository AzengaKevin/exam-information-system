@props([
    'levelUnitId' => null,
    'levels' => [],
    'streams' => []
])

<div wire:ignore.self id="upsert-level-unit-student-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-level-unit-student-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($levelUnitId))
                <h5 id="upsert-level-unit-student-modal-title" class="modal-title">Add Level Unit</h5>
                @else
                <h5 id="upsert-level-unit-student-modal-title" class="modal-title">Take students to the next class</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
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
                    <label for="stream" class="form-label">Stream</label>
                    <select wire:model.lazy="stream_id" id="stream"
                        class="form-select @error('stream_id') is-invalid @enderror">
                        <option value="">-- Select Stream --</option>
                        @foreach ($streams as $stream)
                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                        @endforeach
                    </select>
                    @error('stream_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="alias" class="form-label">Alias</label>
                    <input type="text" wire:model.lazy="alias" id="alias"
                        class="form-control @error('alias') is-invalid @enderror">
                    @error('alias')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
              
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($levelUnitId))
                <button type="type" wire:click="addLevelUnit" class="btn btn-outline-info">Create</button>
                @else
                <button type="type" wire:click="updateLevelUnit" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>