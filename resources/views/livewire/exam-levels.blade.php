<div class="card h-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th colspan="4">Enrolled Levels</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Numeric</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($levels->count())
                    @foreach ($levels as $level)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $level->name }}</td>
                        <td>{{ $level->numeric }}</td>
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
                        <td colspan="6">
                            <div class="py-1 text-center">No Levels added to exam yet</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>