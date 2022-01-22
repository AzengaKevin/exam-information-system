<div>


    <div class="d-flex flex-column flex-md-row justify-content-between alig-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('level-units.index') }}">Classes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Classes</li>
                @endif
            </ol>
        </nav>

        <div class="d-inline-flex gap-2 flex-column flex-md-row align-items-start align-items-md-center">
            <div class="btn-group">
                @can('viewAny', \App\Models\Level::class)
                <a href="{{ route('levels.index') }}" class="btn btn-outline-primary hstack gap-1 align-items-center">
                    <i class="fa fa-list-ul"></i>
                    <span>Levels</span>
                </a>
                @endcan
                @can('viewAny', \App\Models\Stream::class)
                <a href="{{ route('streams.index') }}" class="btn btn-outline-primary hstack gap-1 align-items-center">
                    <i class="fa fa-list-ul"></i>
                    <span>Streams</span>
                </a>
                @endcan
            </div>
            <div class="btn-group">
                @can('create', \App\Models\LevelUnit::class)
                <button data-bs-toggle="modal" data-bs-target="#upsert-level-unit-modal"
                    class="btn btn-outline-primary hstack gap-2 align-items-center">
                    <i class="fa fa-plus"></i>
                    <span>Class</span>
                </button>
                <button data-bs-toggle="modal" data-bs-target="#generate-level-unit-modal"
                    class="btn btn-outline-primary hstack gap-2 align-items-center">
                    <i class="fa fa-cog"></i>
                    <span>Generate Classes</span>
                </button>
                @endcan
            </div>
            @if (!$trashed)
            @can('viewTrashed', \App\Models\LevelUnit::class)
            <a href="{{ route('level-units.index', ['trashed' => true]) }}"
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
                            <th>Level</th>
                            <th>Stream</th>
                            <th>Alias</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($levelUnits->count())
                        @foreach ($levelUnits as $levelUnit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($levelUnit->level)->name }}</td>
                            <td>{{ optional($levelUnit->stream)->name }}</td>
                            <td>{{ $levelUnit->alias ?? 'Not Set' }}</td>
                            <td>{{ $levelUnit->students->count() }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if ($trashed)
                                    @can('restore', $levelUnit)
                                    <button wire:click="restoreLevelUnit({{ $levelUnit->id }})"
                                        class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $levelUnit)
                                    <button wire:click="destroyLevelUnit({{ $levelUnit->id }})"
                                        class="btn btn-sm btn-danger d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-alt"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('view', $levelUnit)
                                    <a href="{{route('level-units.show',$levelUnit)}}"
                                        class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                        <span>Details</span>
                                    </a>
                                    @endcan
                                    @can('update', $levelUnit)                                        
                                    <button wire:click="editLevelUnit({{ $levelUnit }})"
                                        class="btn btn-sm btn-outline-info d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $levelUnit)                                        
                                    <button wire:click="showDeleteLevelUnitModal({{ $levelUnit }})"
                                        class="btn btn-sm btn-outline-danger d-inline-flex gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
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
                            <td colspan="6">
                                <div class="py-1 text-center">No level units {{ $trashed ? "trashed" : "created" }} yet
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{ $levelUnits->links() }}
                                @if ($levelUnits->count())
                                <div class="text-muted">{{ $levelUnits->firstItem() }} - {{ $levelUnits->lastItem() }}
                                    out of
                                    {{ $levelUnits->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


    <x-modals.level-units.upsert :levelUnitId="$levelUnitId" :levels="$levels" :streams="$streams" />

    <x-modals.level-units.delete :alias="$alias" />

</div>