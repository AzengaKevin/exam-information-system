<div wire:ignore.self id="add-student-guardians-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="add-student-guardians-modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form wire:submit.prevent="addStudentGuardians" class="modal-content">
            <div class="modal-header">
                <h5 id="add-student-guardians-modal-title" class="modal-title">Add {{ optional($student)->name ?? 'Student' }} Guardians</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
                        @if ($guardians->count())
                            
                        @foreach ($guardians as $guardian)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedGuardians.{{ $guardian->id }}" id="guardians-{{ $loop->iteration }}" class="form-check-input" value="true">
                                <label for="guardians-{{ $loop->iteration }}" class="form-check-label">{{ $guardian->auth->name }}</label>
                            </div>
                        </div>
                    @endforeach
                        @else
                            <div class="col-md-12">No guardians added yet, <a href="{{ route('guardians.index') }}">Add Guardian</a></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" class="btn btn-outline-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
