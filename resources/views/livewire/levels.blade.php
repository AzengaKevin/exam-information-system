<div>

    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($systemSettings->school_has_streams)
                <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
                @endif
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('levels.index') }}">Levels</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Levels</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex gap-2 align-items-center flex-wrap">
            @can('create', \App\Models\Level::class)
            <button type="button" data-bs-toggle="modal" data-bs-target="#upsert-level-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Level</span>
            </button>
            @endcan
            @can('bulkDelete', \App\Models\Level::class)
            <button type="button" data-bs-toggle="modal" data-bs-target="#truncate-levels-modal"
                class="btn btn-outline-danger d-inline-flex gap-2 align-items-center">
                <i class="fa fa-trash"></i>
                <span>Delete All Levels</span>
            </button>
            @endcan
            @if (!$trashed)
            @can('viewTrashed', \App\Models\Level::class)
            <a href="{{ route('levels.index', ['trashed' => true]) }}"
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
                            <th>Numeric</th>
                            <th>Students</th>
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
                            <td>{{ $level->students->count() }}</td>
                            <td>
                                <div class="d-inline-flex gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $level)                                        
                                    <a href="{{ route('levels.show', $level) }}"
                                        class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                    @endcan
                                    @can('update', $level)                                        
                                    <button wire:click="editLevel({{ $level }})"
                                        class="btn btn-sm btn-outline-info d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $level)                                        
                                    <button wire:click="showDeleteLevelModal({{ $level }})"
                                        class="btn btn-sm btn-outline-danger d-inline-flex gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $level)                                        
                                    <button wire:click="restoreLevel({{ $level->id }})"
                                        class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $level)                                        
                                    <button wire:click="destroyLevel({{ $level->id }})"
                                        class="btn btn-sm btn-danger d-inline-flex gap-1 align-items-center">
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
                                <div class="py-1 text-center">No levels {{ $trashed ? "trashed" : "created" }} yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                {{ $levels->links() }}
                                @if ($levels->count())
                                <div class="text-muted">{{ $levels->firstItem() }} - {{ $levels->lastItem() }} out of
                                    {{ $levels->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.levels.upsert :levelId="$levelId" />
    <x-modals.levels.delete :name="$name" />
    <x-modals.levels.truncate />

</div>