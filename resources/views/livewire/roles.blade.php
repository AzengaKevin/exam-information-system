<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-md-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Roles</li>
                @endif
            </ol>
        </nav>
        <div class="d-inline-flex flex-wrap gap-2 align-items-center">
            @can('viewAny', \App\Models\Permission::class)
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary  hstack gap-2 align-items-center">
                <i class="fa fa-users-cog"></i>
                <span>Permissions</span>
            </a>
            @endcan
            @can('create', \App\Models\Role::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-role-modal"
                class="btn btn-outline-primary hstack gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Role</span>
            </button>
            @endcan

            @if (!$trashed)
            @can('viewTrashed', \App\Models\Role::class)
            <a href="{{ route('roles.index', ['trashed' => true]) }}"
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
                            <th>Permissions</th>
                            <th>Users</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($roles->count())
                        @foreach ($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td>{{ $role->users->count() }}</td>
                            <td>{{ $role->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center">
                                    @if (!$role->trashed())
                                    @can('update', $role)
                                    <button wire:click="editRole({{ $role }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    @endcan
                                    @can('managePermissions', $role)
                                    <button wire:click="showUpdatePermissionsModal({{ $role }})"
                                        class="btn btn-sm btn-outline-success hstack gap-1 align-items-center">
                                        <i class="fa fa-check"></i>
                                        <span>Permissions</span>
                                    </button>
                                    @endcan
                                    @can('delete', $role)
                                    <button wire:click="showDeleteRoleModal({{ $role }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                    @else

                                    @can('restore', $role)
                                    <button wire:click="restoreRole({{ $role->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    @endcan
                                    @can('forceDelete', $role)
                                    <button wire:click="destroyRole({{ $role->id }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-alt" aria-hidden="true"></i>
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
                                <div class="py-1">No roles {{ $trashed ? 'trashed' : 'created' }} yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{ $roles->links() }}
                                @if ($roles->count())
                                <div class="text-muted">{{ $roles->firstItem() }} - {{ $roles->lastItem() }} out of
                                    {{ $roles->total() }}</div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <x-modals.roles.upsert :roleId="$roleId" />
    <x-modals.roles.delete :name="$name" />
    <x-modals.roles.update-permissions :name="$name" :permissions="$permissions" />

</div>