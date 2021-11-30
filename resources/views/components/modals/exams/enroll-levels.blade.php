@props(['shortname', 'levels' => []])

<div wire:ignore.self id="enroll-levels-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="enroll-levels-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <form wire:submit.prevent="updateExamLevels" class="modal-content">
            <div class="modal-header">
                <h5 id="enroll-levels-modal-title" class="modal-title">Enroll {{ $shortname }} Levels</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
                        @foreach ($levels as $level)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedLevels.{{ $level->id }}" id="level-{{ $loop->iteration }}" class="form-check-control" value="true">
                                <label for="level-{{ $loop->iteration }}" class="form-check-label">{{ $level->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-outline-info">Update</button>
            </div>
        </form>
    </div>
</div>