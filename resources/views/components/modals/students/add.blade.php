@props([
'levels' => [],
'hostels' => [],
'streams' => [],
'genderOptions' => [],
'kcpeGradeOptions' => [],
'systemSettings'
])

<div wire:ignore.self id="add-student-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="add-student-modal-title">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="add-student-modal-title" class="modal-title">Add Student (With Guardian)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <fieldset>
                        <h5>Student Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="student-name" class="form-label">Name</label>
                                <input type="text" wire:model.lazy="student.name" id="student-name"
                                    class="form-control @error('student.name') is-invalid @enderror">
                                @error('student.name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            @if ($systemSettings->school_level == 'secondary')
                            <div class="col-md-3">
                                <label for="kcpe_marks" class="form-label">KCPE Marks</label>
                                <input type="number" step="1" min="0" max="500" wire:model.lazy="student.kcpe_marks"
                                    id="student.kcpe_marks" class="form-control @error('student.kcpe_marks') is-invalid @enderror">
                                @error('student.kcpe_marks')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="kcpe-grade" class="form-label">KCPE Grade</label>
                                <select wire:model="student.kcpe_grade" id="kcpe-grade"
                                    class="form-select @error('student.kcpe_grade') is-invalid @enderror">
                                    <option value="">-- Select Grade --</option>
                                    @foreach ($kcpeGradeOptions as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                @error('student.kcpe_grade')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label for="adm-no" class="form-label">Admission Number</label>
                                <input type="text" wire:model.lazy="student.adm_no" id="adm-no"
                                    class="form-control @error('student.adm_no') is-invalid @enderror">
                                @error('student.adm_no')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="upi" class="form-label">UPI</label>
                                <input type="text" wire:model.lazy="student.upi" id="upi"
                                    class="form-control @error('student.upi') is-invalid @enderror">
                                @error('student.upi')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender</label>
                                <select wire:model="student.gender" id="gender"
                                    class="form-select @error('student.gender') is-invalid @enderror">
                                    <option value="">-- Select Gender --</option>
                                    @foreach ($genderOptions as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                @error('student.gender')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="dob" class="form-label">Date Of Birth</label>
                                <input type="date" wire:model.lazy="student.dob" id="dob"
                                    class="form-control @error('student.dob') is-invalid @enderror">
                                @error('student.gender')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="level" class="form-label">Admission Level</label>
                                <select wire:model.lazy="student.admission_level_id" id="level"
                                    class="form-select @error('student.admission_level_id') is-invalid @enderror">
                                    <option value="">-- Select Level --</option>
                                    @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                @error('student.admission_level_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            @if ($systemSettings->school_has_streams)
                            <div class="col-md-6">
                                <label for="stream" class="form-label">Stream</label>
                                <select wire:model.lazy="student.stream_id" id="stream"
                                    class="form-select @error('student.stream_id') is-invalid @enderror">
                                    <option value="">-- Select Stream --</option>
                                    @foreach ($streams as $stream)
                                    <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                    @endforeach
                                </select>
                                @error('student.stream_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-9">
                                <label for="hostel" class="form-label">Hostel</label>
                                <select wire:model="student.hostel_id" id="hostel"
                                    class="form-select @error('student.hostel_id') is-invalid @enderror">
                                    <option value="">-- Select Hostel --</option>
                                    @foreach ($hostels as $hostel)
                                    <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                                    @endforeach
                                </select>
                                @error('student.stream_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea wire:model.lazy="student.description" id="description" cols="100" rows="3"
                                    class="form-control @error('student.description') is-invalid @enderror">
                                </textarea>
                                @error('student.description')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <h5>Guardian Details</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="guardian-name" class="form-label">Name</label>
                                <input type="text" wire:model.lazy="guardian.name" id="guardian-name"
                                    class="form-control @error('guardian.name') is-invalid @enderror">
                                @error('guardian.name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" wire:model.lazy="guardian.phone" id="phone" aria-describedby="phone-help"
                                    class="form-control @error('guardian.phone') is-invalid @enderror"
                                    placeholder="254707427854">
                                <div id="phone-help" class="form-text">Begin with the Kenyas country code(254) without the (+) symbol.</div>
                                @error('guardian.phone')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" wire:model.lazy="guardian.email" id="email"
                                    class="form-control @error('guardian.email') is-invalid @enderror">
                                @error('guardian.email')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="profession" class="form-label">Profession</label>
                                <input type="text" wire:model.lazy="guardian.profession" id="profession"
                                    class="form-control @error('guardian.profession') is-invalid @enderror">
                                @error('guardian.profession')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" wire:model.lazy="guardian.location" id="location"
                                    class="form-control @error('guardian.location') is-invalid @enderror">
                                @error('guardian.location')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="button" wire:click="newAddStudent" class="btn btn-outline-primary">Submit</button>
            </div>
        </div>
    </div>
</div>