<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Authorization</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex flex-wrap gap-2 align-items-md-center">
            <button data-bs-toggle="modal" data-bs-target="#upsert-permission-modal"
                class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Permission</span>
            </button>
            @if (!$trashed)
            @can('viewTrashed', \App\Models\Permission::class)
            <a href="{{ route('permissions.index', ['trashed' => true]) }}"
                class="btn btn-warning d-inline-flex flex align-items-center">
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
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($permissions->count())
                        @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->slug }}</td>
                            <td>
                                @if (!$trashed)
                                <div class="hstack gap-2 align-items-center">
                                    @can('update', $permission)
                                    <button wire:click="editPermission({{ $permission }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('updateLocked', $permission)
                                    <button wire:click="togglePermissionLockedStatus({{ $permission }})"
                                        class="btn btn-sm btn-{{ $permission->locked ? "warning" : "outline-warning" }} d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>{{ $permission->locked ? "Unlock" : "Lock" }}</span>
                                    </button>
                                    @endcan
                                    @can('delete', $permission)
                                    <button wire:click="showDeletePermissionModal({{ $permission }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                </div>
                                @else
                                @can('restore', $permission)
                                <button wire:click="restorePermission({{ $permission }})"
                                    class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                    <i class="fa fa-trash-restore-alt"></i>
                                    <span>Restore</span>
                                </button>
                                @endcan
                                @can('forceDelete', $permission)
                                <button wire:click="destroyPermissionModal({{ $permission }})"
                                    class="btn btn-sm btn-danger d-inline-flex gap-2 align-items-center">
                                    <i class="fa fa-trash-alt" aria-hidden="true"></i>
                                    <span>Destroy</span>
                                </button>
                                @endcan
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">
                                <div class="py-1">No Permission created yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                {{ $permissions->links() }}
                                @if ($permissions->count())
                                <div class="text-muted">{{ $permissions->firstItem() }} - {{ $permissions->lastItem() }}
                                    out of
                                    {{ $permissions->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.permissions.upsert :permissionId="$permissionId" />
    <x-modals.permissions.delete :name="$name" />

</div>