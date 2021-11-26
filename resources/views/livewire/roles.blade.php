<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Description</th>
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
                    <td>{{ $role->description }}</td>
                    <td>{{ $role->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button wire:click="editRole({{ $role }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showUpdatePermissionsModal({{ $role }})" class="btn btn-sm btn-outline-success hstack gap-1 align-items-center">
                                <i class="fa fa-check"></i>
                                <span>Permissions</span>
                            </button>
                            <button wire:click="showDeleteRoleModal({{ $role }})" class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5">
                        <div class="py-1">No Roles created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
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

    <x-modals.roles.upsert :roleId="$roleId" />
    <x-modals.roles.delete :name="$name" />
    <x-modals.roles.update-permissions :name="$name" :permissions="$permissions" />
    
</div>