<div class="row g-3">

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title">Compulsory Subjects</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Shortname</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($compulsorySubjects->count())
                            @foreach ($compulsorySubjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ optional($subject->department)->name ?? 'Not Set' }}</td>
                                <td>{{ $subject->shortname }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">
                                    <div class="py-1 text-center">No compulsory subjects</div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title">Optional Subjects</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Shortname</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($optionalSubjects->count())
                            @foreach ($optionalSubjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ optional($subject->department)->name ?? 'Not Set' }}</td>
                                <td>{{ $subject->shortname }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">
                                    <div class="py-1 text-center">No optional subjects</div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>