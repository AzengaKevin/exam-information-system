<div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('gradings.index') }}">Grading Systems</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Grading Systems</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex flex-wrap gap-2 align-items-center">
            @can('create', \App\Models\Grading::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-grading-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>System</span>
            </button>
            @endcan
            @if (!$trashed)
            @can('viewTrashed', \App\Models\Grading::class)
            <a href="{{ route('gradings.index', ['trashed' => true]) }}"
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
                        <th>#</th>
                        <th>Name</th>
                        <th>Updated?</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @if ($gradings->count())
                        @foreach ($gradings as $grading)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $grading->name }}</td>
                            <td>{{ $grading->updated_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $grading)
                                    <button wire:click="showGrading({{ $grading }})"
                                        class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </button>
                                    @endcan
                                    @can('update', $grading)
                                    <button wire:click="editGrading({{ $grading }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $grading)
                                    <button wire:click="showDeleteGradingModal({{ $grading }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $grading)
                                    <button wire:click="restoreGrading({{ $grading->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $grading)
                                    <button wire:click="destroyGrading({{ $grading->id }})"
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
                            <td colspan="4">
                                <div class="py-1 text-center">No grading system has been
                                    {{ $trashed ? "trashed" : "added" }}
                                    yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.gradings.upsert :gradingId="$gradingId" :grades="$grades" :values="$values" />
    <x-modals.gradings.delete :name="$name" />
    <x-modals.gradings.show :name="$name" :values="$values" />
</div>