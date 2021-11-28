<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Responsibility</th>
                    <th>Subject</th>
                    <th>Level Unit</th>
                    <th>Level</th>
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
                    <td>{{ $responsibility->pivot->levelUnit->alias }}</td>
                    <td>{{ $responsibility->pivot->level->name }}</td>
                    <td>{{ $responsibility->pivot->department->name }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-cross" aria-hidden="true"></i>
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

    {{-- <x-modals.responsibilities.upsert :responsibilityId="$responsibilityId" /> --}}
    {{-- <x-modals.responsibilities.delete :name="$name" /> --}}
    
</div>