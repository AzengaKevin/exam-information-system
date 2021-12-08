@props(['name', 'subjects' => []])

<div wire:ignore.self id="update-teacher-subjects-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="update-teacher-subjects-modal-title">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="updateSubjects" class="modal-content">
            <div class="modal-header">
                <h5 id="update-teacher-subjects-modal-title" class="modal-title">Update {{ $name }} Subjects</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
                        @foreach ($subjects as $subject)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedSubjects.{{ $subject->id }}" id="subject-{{ $loop->iteration }}" class="form-check-control" value="true">
                                <label for="subject-{{ $loop->iteration }}" class="form-check-label">{{ $subject->name }}</label>
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