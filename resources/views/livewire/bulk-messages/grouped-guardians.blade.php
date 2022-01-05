<div wire:ignore.self id="grouped-guardians-bulk-sms-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="grouped-guardians-bulk-sms-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="grouped-guardians-bulk-sms-modal-title" class="modal-title">Send Bulk Messages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="group-by" class="form-label fw-bold">Group By</label>
                    <fieldset id="group-by" class="d-flex flex-wrap gap-2">
                        <div class="form-check">
                            <input type="radio" wire:model="groupBy" id="all-guardians" class="form-check-input"
                                value="all" checked>
                            <label for="all-guardians" class="form-check-label">All Guardians</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" wire:model="groupBy" id="level-guardians" class="form-check-input"
                                value="levels">
                            <label for="level-guardians" class="form-check-label">Levels</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" wire:model="groupBy" id="stream-guardians" class="form-check-input"
                                value="streams">
                            <label for="stream-guardians" class="form-check-label">Streams</label>
                        </div>
                    </fieldset>
                </div>

                @if ($groupBy == 'levels')
                <div class="mt-3">
                    <label for="levels" class="form-label fw-bold">Levels</label>
                    <fieldset class="row g-2" id="levels">
                        @foreach ($levels as $level)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedLevels.{{ $level->id }}"
                                    id="level-{{ $loop->iteration }}" class="form-check-input" value="true">
                                <label for="level-{{ $loop->iteration }}" class="form-check-label">{{ $level->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </fieldset>
                </div>
                @endif

                @if ($groupBy == 'streams')
                <div class="mt-3">
                    <label for="levels" class="form-label fw-bold">Streams</label>
                    <fieldset class="row g-2" id="streams">
                        @foreach ($levelUnits as $levelUnit)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" wire:model="selectedLevelUnits.{{ $levelUnit->id }}"
                                    id="stream-{{ $loop->iteration }}" class="form-check-input" value="true">
                                <label for="stream-{{ $loop->iteration }}"
                                    class="form-check-label">{{ $levelUnit->alias }}</label>
                            </div>
                        </div>
                        @endforeach
                    </fieldset>
                </div>
                @endif

                <div class="mt-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea wire:model.lazy="content" id="content" cols="100" rows="5"
                        class="form-control @error('content') is-invalid @enderror"></textarea>
                    @error('content')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
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