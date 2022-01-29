<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table {{ $type }} table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Responsibility</th>
                    <th>Subject</th>
                    <th>Class</th>
                    @if ($systemSettings->school_has_streams)
                    <th>Level</th>
                    @endif
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($responsibilities->count())
                @foreach ($responsibilities as $responsibility)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $responsibility->name }}</td>
                    <td>{{ $responsibility->pivot->subject->name }}</td>
                    @if ($systemSettings->school_has_streams)
                    <td>{{ $responsibility->pivot->levelUnit->alias }}</td>
                    @endif
                    <td>{{ $responsibility->pivot->level->name }}</td>
                    <td>{{ $responsibility->pivot->department->name }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <button wire:click="removeResponsibility({{ $responsibility->pivot->id }})"
                                class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-times" aria-hidden="true"></i>
                                <span>Remove</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7">
                        <div class="py-1 text-center">No Responsibility created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-modals.teachers.responsibilities.assign 
        :levels="$levelsToShow" 
        :departments="$departments"
        :levelUnits="$levelUnitsToShow" 
        :responsibilityOptions="$allResponsibilities" :subjects="$subjects"
        :fields="$fields" />

    @include('partials.teachers.responsibilities.assign-bulk')
</div>