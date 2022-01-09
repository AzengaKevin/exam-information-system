<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Adm. No</th>
                    @if ($systemSettings->school_level == 'secondary')
                    <th>KCPE</th>
                    @endif
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
                    <td>{{ $student->adm_no ?? '-' }}</td>
                    @if ($systemSettings->school_level == 'secondary')
                    <td>{{ $student->kcpe_marks }}</td>
                    @endif
                    <td>{{ $student->name }}</td>
                    @if ($systemSettings->school_has_streams)
                    <td>{{ optional($student->levelUnit)->alias ?? 'N/A' }}</td>
                    @else
                    <td>{{ optional($student->level)->name ?? 'N/A' }}</td>
                    @endif
                    <td>{{ optional($student->dob)->diffInYears(now()) }}</td>
                    <td>{{ $student->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <a href="{{route('students.show', $student)}}"
                                class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
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
                            <button wire:click="showDeleteStudentModal({{ $student }})"
                                class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                <i class="fa fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="{{ $systemSettings->school_level == 'secondary' ? '8' : '7' }}">
                        <div class="py-1 text-center">No Student Added Yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $systemSettings->school_level == 'secondary' ? '8' : '7' }}">
                        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                            {{ $students->links() }}
                            @if ($students->count())
                            <div class="text-muted ms-md-3">{{ $students->firstItem() }} - {{ $students->lastItem() }}
                                out of
                                {{ $students->total() }}</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <x-modals.students.upsert :studentId="$studentId" :streams="$streams" :levels="$levels" :hostels="$hostels"
        :genderOptions="$genderOptions" :kcpeGradeOptions="$kcpeGradeOptions" :systemSettings="$systemSettings" />

    <x-modals.students.add :studentId="$studentId" :streams="$streams" :levels="$levels" :hostels="$hostels"
        :genderOptions="$genderOptions" :kcpeGradeOptions="$kcpeGradeOptions" :systemSettings="$systemSettings" />

    <x-modals.students.delete :name="$name" />

    <x-modals.students.export-spreadsheet />
    <x-modals.students.import-spreadsheet />

</div>