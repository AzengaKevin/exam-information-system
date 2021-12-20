@props([
'studentId' => null,
'levels' => [],
'hostels' => [],
'streams' => [],
'genderOptions' => [],
'kcpeGradeOptions' => []
])

<div wire:ignore.self id="upsert-student-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-student-modal-title">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($studentId))
                <h5 id="upsert-student-modal-title" class="modal-title">Add Student</h5>
                @else
                <h5 id="upsert-student-modal-title" class="modal-title">Update Student</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" wire:model.lazy="name" id="name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="kcpe_marks" class="form-label">KCPE Marks</label>
                            <input type="number" step="1" min="0" max="500" wire:model.lazy="kcpe_marks" id="kcpe_marks"
                                class="form-control @error('kcpe_marks') is-invalid @enderror">
                            @error('kcpe_marks')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="kcpe-grade" class="form-label">KCPE Grade</label>
                            <select wire:model="kcpe_grade" id="kcpe-grade"
                                class="form-select @error('kcpe_grade') is-invalid @enderror">
                                <option value="">-- Select Grade --</option>
                                @foreach ($kcpeGradeOptions as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            @error('kcpe_grade')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="adm-no" class="form-label">Admission Number</label>
                            <input type="text" wire:model.lazy="adm_no" id="adm-no"
                                class="form-control @error('adm_no') is-invalid @enderror">
                            @error('adm_no')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="upi" class="form-label">UPI</label>
                            <input type="text" wire:model.lazy="upi" id="upi"
                                class="form-control @error('upi') is-invalid @enderror">
                            @error('upi')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select wire:model="gender" id="gender"
                                class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Select Gender --</option>
                                @foreach ($genderOptions as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            @error('gender')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date Of Birth</label>
                            <input type="date" wire:model.lazy="dob" id="dob"
                                class="form-control @error('dob') is-invalid @enderror">
                            @error('gender')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        @if (is_null($studentId))
                        <div class="col-md-6">
                            <label for="level" class="form-label">Admission Level</label>
                            <select wire:model.lazy="admission_level_id" id="level"
                                class="form-select @error('admission_level_id') is-invalid @enderror">
                                <option value="">-- Select Level --</option>
                                @foreach ($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('admission_level_id')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        @else
                        <div class="col-md-6">
                            <label for="level" class="form-label">Current Level</label>
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
                        @endif

                        <div class="col-md-6">
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

                        <div class="col-md-9">
                            <label for="hostel" class="form-label">Hostel</label>
                            <select wire:model="hostel_id" id="hostel"
                                class="form-select @error('hostel_id') is-invalid @enderror">
                                <option value="">-- Select Hostel --</option>
                                @foreach ($hostels as $hostel)
                                <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                                @endforeach
                            </select>
                            @error('stream_id')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea wire:model.lazy="description" id="description" cols="100" rows="3"
                                class="form-control @error('description') is-invalid @enderror">
                            </textarea>
                            @error('description')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>

                @if (is_null($studentId))
                <button type="button" wire:click="addStudent" class="btn btn-outline-primary">Submit</button>
                @else
                <button type="button" wire:click="updateStudent" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>