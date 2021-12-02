<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Admission Number</th>
                    <th>Guardian</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($students->count())
                @foreach ($students as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->adm_no }}</td>
                    <td>  @foreach ($student->guardians as $guardian)
                        {{$guardian->auth->name}}
                         @endforeach</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <a href="{{route('students.show',['student'=>$student->adm_no])}}"  class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                
                                <span>Profile</span>
                            </a>
                            <button wire:click="editstudent({{ $student }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeletestudentModal({{ $student }})"  class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6">
                        <div class="py-1 text-center">No student in this classroom yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        {{ $students->links() }}
                        @if ($students->count())
                        <div class="text-muted">{{ $students->firstItem() }} - {{ $students->lastItem() }} out of
                            {{ $students->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <x-modals.level-unit-students.upsert 
        :levelUnitId="$levelUnitId"
        :levels="$levels"
        :streams="$streams" />
</div>