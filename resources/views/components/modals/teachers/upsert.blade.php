@props([
'teacherId' => null,
'employers' => []
])

<div wire:ignore.self id="upsert-teacher-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-teacher-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($teacherId))
                <h5 id="upsert-teacher-modal-title" class="modal-title">Add Teacher</h5>
                @else
                <h5 id="upsert-teacher-modal-title" class="modal-title">Update Teacher</h5>
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
                    <label for="email" class="form-label">Email</label>
                    <input type="email" wire:model.lazy="email" id="email"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="employer" class="form-label">Employer</label>
                    <select wire:model="employer" id="employer"
                        class="form-select @error('employer') is-invalid @enderror">
                        <option value="">-- Select Employer --</option>
                        @foreach ($employers as $employer)
                        <option value="{{ $employer }}">{{ $employer }}</option>
                        @endforeach
                    </select>
                    @error('employer')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mt-3">
                    <label for="tsc-number" class="form-label">TSC Number</label>
                    <input type="text" wire:model.lazy="tsc_number" id="tsc-number"
                        class="form-control @error('tsc_number') is-invalid @enderror">
                    @error('tsc_number')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>

                @if (is_null($teacherId))
                <button type="button" wire:click="addTeacher" class="btn btn-outline-primary">Submit</button>
                @else
                <button type="button" wire:click="updateTeacher" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>