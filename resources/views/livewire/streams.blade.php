<div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('streams.index') }}">Streams</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Streams</li>
                @endif
            </ol>
        </nav>

        <div class="d-inline-flex gap-2 align-items-center flex-wrap">
            @can('create', \App\Models\Stream::class)
            <button type="button" data-bs-toggle="modal" data-bs-target="#upsert-stream-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Stream</span>
            </button>
            @endcan
            @can('bulkDelete', \App\Models\Stream::class)
            <button type="button" data-bs-toggle="modal" data-bs-target="#truncate-streams-modal"
                class="btn btn-outline-danger d-inline-flex gap-2 align-items-center">
                <i class="fa fa-trash"></i>
                <span>Delete All Streams</span>
            </button>
            @endcan
            @if (!$trashed)
            @can('viewTrashed', \App\Models\Stream::class)
            <a href="{{ route('streams.index', ['trashed' => true]) }}"
                class="btn btn-warning d-inline-flex gap-1 align-items-center">
                <i class="fa fa-eye"></i>
                <span>Trashed</span>
            </a>
            @endcan
            @endif
        </div>
    </div>
    <hr>

    <x-feedback />


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Alias</th>
                            <th>Students</th>
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
                            <td>{{ $stream->students->count() }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $stream)                  
                                    <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </button>
                                    @endcan
                                    @can('update', $stream)                                        
                                    <button wire:click="editStream({{ $stream }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $stream)                                        
                                    <button wire:click="showDeleteStreamModal({{ $stream }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $stream)                                        
                                    <button wire:click="restoreStream({{ $stream->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $stream)                                        
                                    <button wire:click="destroyStream({{ $stream->id }})"
                                        class="btn btn-sm btn-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-alt"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">
                                <div class="py-1 text-center">No streams {{ $trashed ? "trashed" : "created" }} yet
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
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
        </div>
    </div>

    <x-modals.streams.upsert :streamId="$streamId" :optionalSubjects="$optionalSubjects" />
    <x-modals.streams.delete :name="$name" />
    <x-modals.streams.truncate />

</div>