<div class="card h-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="5">Enrolled Subject</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Shortname</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($subjects->count())
                    @foreach ($subjects as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{ $subject->shortname }}</td>
                        <td>
                            <div class="hstack gap-2 align-items-center">
                                <button class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
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
                            <div class="py-1 text-center">No Subject created yet</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>