<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover text-center">
                <thead>
                    <tr>
                        <th colspan="5">Students</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
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
                        <td></td>
                        <td>
                            <div class="hstack gap-2 align-items-center justify-content-center">
                                <a href="{{route('students.show',['student' => $student])}}"
                                    class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                    <i class="fa fa-eye"></i>
                                    <span>Profile</span>
                                </a>
                                <button class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                    <i class="far fa-chart-bar"></i>
                                    <span>Analysis</span>
                                </button>
                                <button class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                    <i class="fa fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5">
                            <div class="py-1 text-center">No student in this classroom yet</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
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
    </div>
</div>
