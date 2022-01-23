<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('guardians.index') }}">Guardians</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Guardians</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex flex-wrap gap-2 align-items-center">
            @can('create', \App\Models\Guardian::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-guardian-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Guardian</span>
            </button>
            @endcan
            @if (!$trashed)                
            @can('viewTrashed', \App\Models\Guardian::class)
            <a href="{{ route('guardians.index', ['trashed' => true]) }}"
                class="btn btn-warning d-inline-flex gap-1 align-items-center">
                <i class="fa fa-eye"></i>
                <span>Trash</span>
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
                            <th>Phone</th>
                            <th>Profession</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($guardians->count())
                        @foreach ($guardians as $guardian)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($guardian->auth)->name }}</td>
                            <td>{{ optional($guardian->auth)->phone }}</td>
                            <td>{{ $guardian->profession }}</td>
                            <td>{{ $guardian->location }}</td>
                            <td>
                                <div class="d-inline-flex flex-wrap gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('view', $guardian)
                                    <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </button>
                                    @endcan
                                    @can('update', $guardian)
                                    <button wire:click="editGuardian({{ $guardian }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('delete', $guardian)
                                    <button wire:click="showDeleteGuardianModal({{ $guardian }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-alt"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $guardian)
                                    <button wire:click="restoreGuardian({{ $guardian->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $guardian)
                                    <button wire:click="destroyGuardian({{ $guardian->id }})"
                                        class="btn btn-sm btn-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-alt"></i>
                                        <span>Destroy</span>
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
                                <div class="py-1 text-center">No guardians {{ $trashed ? "trashed yet" : "added yet" }}
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{ $guardians->links() }}
                                @if ($guardians->count())
                                <div class="text-muted">{{ $guardians->firstItem() }} - {{ $guardians->lastItem() }} out
                                    of
                                    {{ $guardians->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.guardians.upsert :guardianId="$guardianId" />
    <x-modals.guardians.delete :name="$name" />

</div>