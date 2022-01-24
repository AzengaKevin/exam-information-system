<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('responsibilities.index') }}">Responsibilities</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Responsibilities</li>
                @endif
            </ol>
        </nav>

        <div class="d-inline-flex flex-wrap gap-2 align-items-md-center">
            @can('create', \App\Models\Responsibility::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-responsibility-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Responsibility</span>
            </button>
            @endcan

            @if (!$trashed)
            @can('viewTrashed', \App\Models\Responsibility::class)
            <a href="{{ route('responsibilities.index', ['trashed' => true]) }}"
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
                            <th>Requiremnents</th>
                            <th>Teachers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($responsibilities->count())
                        @foreach ($responsibilities as $responsibility)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $responsibility->name }}</td>
                            <td>{{ implode(', ', $responsibility->requirements ?? []) }}</td>
                            <td>{{ $responsibility->teachers->count() }}</td>
                            <td>
                                <div class="d-inline-flex gap-2 align-items-center">
                                    @if (!$trashed)
                                    @can('update', $responsibility)
                                    <button wire:click="editResponsibility({{ $responsibility }})"
                                        class="btn btn-sm btn-outline-info d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('updateLocked', $responsibility)
                                    <button wire:click="toggleResponsibilityLock({{ $responsibility }})"
                                        class="btn btn-sm {{ $responsibility->locked ? 'btn-warning' : 'btn-outline-warning' }} d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        @if ($responsibility->locked)
                                        <span>Unlock</span>
                                        @else
                                        <span>Lock</span>
                                        @endif
                                    </button>
                                    @endcan
                                    @can('delete', $responsibility)
                                    <button wire:click="showDeleteResponsibilityModal({{ $responsibility }})"
                                        class="btn btn-sm btn-outline-danger d-inline-flex gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else
                                    @can('restore', $responsibility)
                                    <button wire:click="restoreResponsibility({{ $responsibility->id }})"
                                        class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $responsibility)
                                    <button wire:click="destroyResponsibility({{ $responsibility->id }})"
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
                                <div class="py-1">No Responsibility created yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.responsibilities.upsert :responsibilityId="$responsibilityId" :requirementOptions="$requirementOptions" />
    <x-modals.responsibilities.delete :name="$name" />

</div>