@props(['messageId' => null, 'users' => []])

<div wire:ignore.self id="upsert-message-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-message-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($messageId))
                <h5 id="upsert-message-modal-title" class="modal-title">Add Message</h5>
                @else
                <h5 id="upsert-message-modal-title" class="modal-title">Update Message</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label for="recipient" class="form-label">Recipient</label>
                    <select wire:model="recipient_id" id="recipient"
                        class="form-select @error('recipient_id') is-invalid @enderror">
                        <option value="">-- Select --</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('recipient_id')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea wire:model.lazy="content" id="content" cols="100" rows="3" class="form-control @error('content') is-invalid @enderror"></textarea>
                    @error('content')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                @if(is_null($messageId))
                <button type="submit" wire:click="createMessage" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateMessage" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>