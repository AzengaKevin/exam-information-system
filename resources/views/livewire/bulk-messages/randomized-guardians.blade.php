<div wire:ignore.self id="randomized-guardians-bulk-sms-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="randomized-guardians-bulk-sms-modal-title">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="randomized-guardians-bulk-sms-modal-title" class="modal-title">Send Messages</h5>
                <button type="button" data-bs-dismiss="modal" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea wire:model.lazy="content" id="content" cols="100" rows="5"
                        class="form-control @error('content') is-invalid @enderror"></textarea>
                    @error('content')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="students" class="form-label fw-bold">Select Students Whos Guardians Will Receive The
                        Message</label>
                    <fieldset id="students" class="row g-3">
                        @foreach ($students as $student)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedStudents.{{ $student->id }}"
                                    id="student-{{ $loop->iteration }}-check" class="form-check-input">
                                <label for="student-{{ $loop->iteration }}-check"
                                    class="form-check-label">{{ $student->name }}</label>
                            </div>
                        </div>
                        @endforeach
                        {{ $students->links() }}
                    </fieldset>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="sendMessages"
                    class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                    <i class="fa fa-paper-plane"></i>
                    <span>Send Message</span>
                </button>
            </div>
        </div>
    </div>
</div>