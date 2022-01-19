@props([
'examId' => null,
'terms',
'examStatusOptions',
'levels' => [],
'subjects' => [],
'otherExams',
])

<div wire:ignore.self id="upsert-exam-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="upsert-exam-modal-title">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if (is_null($examId))
                <h5 id="upsert-exam-modal-title" class="modal-title">Add Exam</h5>
                @else
                <h5 id="upsert-exam-modal-title" class="modal-title">Update Exam</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="shortname" class="form-label">Shortname</label>
                            <input type="text" wire:model.lazy="shortname" id="shortname"
                                class="form-control @error('shortname') is-invalid @enderror">
                            @error('shortname')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-9">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" wire:model.lazy="name" id="name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3"></div>

                        <div class="col-md-3">
                            <label for="year" class="form-label">Year</label>
                            <input type="text" wire:model.lazy="year" id="year"
                                class="form-control @error('year') is-invalid @enderror">
                            @error('year')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="term" class="form-label">Term</label>
                            <select class="form-control" wire:model.lazy="term" id="term">
                                <option value="">--select term--</option>
                                @foreach($terms as $term)
                                <option value="{{$term}}">{{$term}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="Start Date" class="form-label">Start Date</label>
                            <input type="date" wire:model.lazy="start_date" id="start_date"
                                class="form-control @error('start_date') is-invalid @enderror">
                            @error('start_date')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" wire:model.lazy="end_date" id="end_date"
                                class="form-control @error('end_date') is-invalid @enderror">
                            @error('end_date')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        @if ($otherExams->count())
                        <div class="col-md-12">
                            <label for="deviation-exam" class="form-label">Deviation Exam</label>
                            <select wire:model="deviation_exam_id" id="deviation-exam" class="form-select">
                                <option value="">-- Select Deviation Exam --</option>
                                @foreach ($otherExams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if (false)
                        <div class="col-md-12">
                            <label for="weight" class="form-label">Weight the Exam has on Report Form</label>
                            <input type="number" step="0.01" wire:model.lazy="weight" id="weight"
                                class="form-control @error('weight') is-invalid @enderror">
                            @error('weight')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model.lazy="counts" id="counts"
                                    value="true">
                                <label for="counts" class="form-check-label">Counts on Report Form</label>
                            </div>
                        </div>
                        @endif

                        @if (!is_null($this->examId))
                        <div class="col-md-12">
                            <div>
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @" wire:model="status" id="status">
                                    <option value="">-- Select Status --</option>
                                    @foreach ($examStatusOptions as $status)
                                    <option value="{{$status}}">{{$status}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-12">
                            <label for="levels" class="form-label fw-bold">Select Levels Taking Exam</label>
                            <fieldset id="levels" class="row g-3">
                                @foreach ($levels as $level)
                                <div class="col-md-6">
                                    <div class="form-check ps-0">
                                        <input type="checkbox" wire:model="selectedLevels.{{ $level->id }}"
                                            id="level-{{ $loop->iteration }}" class="form-check-control" value="true">
                                        <label for="level-{{ $loop->iteration }}"
                                            class="form-check-label">{{ $level->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <label for="subjects" class="form-label fw-bold">Select Subjects Taking Exams</label>
                            <fieldset id="subjects" class="row g-3">
                                @foreach ($subjects as $subject)
                                <div class="col-md-4">
                                    <div class="form-check ps-0">
                                        <input type="checkbox" wire:model="selectedSubjects.{{ $subject->id }}"
                                            id="subject-{{ $loop->iteration }}" class="form-check-control" value="true">
                                        <label for="subject-{{ $loop->iteration }}"
                                            class="form-check-label">{{ $subject->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </fieldset>
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Notes</label>
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
                @if(is_null($examId))
                <button type="submit" wire:click="createExam" class="btn btn-outline-info">Create</button>
                @else
                <button type="submit" wire:click="updateExam" class="btn btn-outline-info">Update</button>
                @endif
            </div>
        </div>
    </div>
</div>