<div class="">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sender</th>
                            <th>Recipient</th>
                            <th>Type</th>
                            <th>Sent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($messages->count())
                        @foreach ($messages as $message)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $message->sender->name }}</td>
                            <td>{{ $message->recipient->name }}</td>
                            <td>{{ $message->type }}</td>
                            <td>{{ $message->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-inline-flex gap-2 align-items-center">
                                    @if (!$trashed)
                                    <button class="btn btn-sm btn-outline-primary hstack gap-2 align-items-center">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        <span>Details</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @else
                                    <button class="btn btn-sm btn-danger d-inline-flex gap-2 align-items-center">
                                        <i class="fa fa-trash-alt" aria-hidden="true"></i>
                                        <span>Destroy</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6">No messages have been through the system yet</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">{{ $messages->links() }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>