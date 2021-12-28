<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Alias</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($streams->count())
                @foreach ($streams as $stream)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $stream->name }}</td>
                    <td>{{ $stream->alias }}</td>
                    <td>{{ $stream->description }}</td>
                    <td>{{ $stream->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editStream({{ $stream }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeleteStreamModal({{ $stream }})" class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
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
                        <div class="py-1 text-center">No Streams created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        {{ $streams->links() }}
                        @if ($streams->count())
                        <div class="text-muted">{{ $streams->firstItem() }} - {{ $streams->lastItem() }} out of
                            {{ $streams->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <x-modals.streams.upsert :streamId="$streamId" />
    <x-modals.streams.delete :name="$name" />
    <x-modals.streams.truncate />
    
</div>