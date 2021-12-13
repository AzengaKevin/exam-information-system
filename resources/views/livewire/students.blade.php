<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Adm. No</th>
                    <th>KCPE</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Age</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($students->count())
                @foreach ($students as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->adm_no }}</td>
                    <TD>{{ $student->kcpe_marks }}</TD>
                    <td>{{ $student->name }}</td>
                    <td>{{ optional($student->levelUnit)->alias ?? 'N/A' }}</td>
                    <td>{{ $student->dob->diffInYears(now()) }}</td>
                    <td>{{ $student->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <a href="{{route('students.show',['student'=>$student->adm_no])}}" class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </a>
                            <button wire:click="showAddStudentGuardiansModal({{ $student }})"
                                class="btn btn-sm btn-outline-success hstack gap-1 align-items-center">
                                <i class="fa fa-plus"></i>
                                <span>Guardians</span>
                            </button>
                            <button wire:click="editStudent({{ $student }})"
                                class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeleteStudentModal({{ $student }})" class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                <i class="fa fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="8">
                        <div class="py-1 text-center">No Student Added Yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
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
    
    <x-modals.students.upsert 
        :studentId="$studentId"
        :streams="$streams"
        :levels="$levels"
        :genderOptions="$genderOptions"
        :kcpeGradeOptions="$kcpeGradeOptions" />

    <x-modals.students.delete :name="$name" />
    
    <x-modals.students.export-spreadsheet />
    <x-modals.students.import-spreadsheet />

</div>