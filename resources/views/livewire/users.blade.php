<div class="row g-3">

    <div class="col-md-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-md-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    @if ($trashed)
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Trash</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                    @endif
                </ol>
            </nav>
    
            <div class="d-inline-flex gap-2 align-items-center">
                @can('bulkUpdate', \App\Models\User::class)
                @if (count($selectedUsers ?? []))
                <button class="btn btn-outline-primary d-inline-flex gap-1 align-items-center" data-bs-toggle="modal"
                    data-bs-target="#users-bulk-role-update-modal">
                    <i class="fa fa-pencil-alt"></i>
                    <span>Role</span>
                </button>
                @endif
                @endcan
                @if (!$trashed)
                @can('viewTrashed', \App\Models\User::class)
                <a href="{{ route('users.index', ['trashed' => true]) }}"
                    class="btn btn-warning d-inline-flex gap-1 align-items-center">
                    <i class="fa fa-eye"></i>
                    <span>Trashed</span>
                </a>
                @endcan
                @endif
            </div>
        </div>
        <x-feedback />
    </div>
    <div class="col-md-12">
        <div class="card">
            @if (!$trashed)
            <div class="card-header bg-white d-flex flex-wrap gap-2">
                @foreach ($roles as $role)
                <a href="{{ route('users.index', ['role_id' => $role->id]) }}"
                    class="btn btn-outline-primary d-inline-flex gap-2 align-items-center">
                    <span>{{ $role->name }}</span>
                </a>
                @endforeach
            </div>
            @endif
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Role</th>
                                <th>Active?</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($users->count())
                            @foreach ($users as $user)
                            <tr>
                                <td><input type="checkbox" class="form-check" wire:model="selectedUsers.{{ $user->id }}">
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ ucfirst($user->authenticatable_type ?? 'Anonymous') }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>{{ $user->active ? 'True' : 'False' }}</td>
                                <td>
                                    <div class="d-inline-flex flex-wrap gap-2 align-items-center">
                                        @if (!$trashed)
                                        <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                            <i class="fa fa-eye"></i>
                                            <span>Details</span>
                                        </button>
                                        <button wire:click="editUser({{ $user }})"
                                            class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                            <i class="fa fa-edit"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button wire:click="toggleUserActiveStatus({{ $user }})"
                                            class="btn btn-sm btn-outline-{{ $user->active ? 'warning' : 'success' }} hstack gap-1 align-items-center">
                                            <i class="fa fa-edit"></i>
                                            <span>{{ $user->active ? 'Deativate' : 'Activate' }}</span>
                                        </button>
                                        <button wire:click="resetPassword({{ $user }})"
                                            class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                            <i class="fa fa-lock"></i>
                                            <span>Reset</span>
                                        </button>
                                        <button wire:click="showDeleteUserModal({{ $user }})"
                                            class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                            <i class="fa fa-trash-alt"></i>
                                            <span>Delete</span>
                                        </button>
                                        @else
                                        <button wire:click="restoreUser({{ $user->id }})"
                                            class="btn btn-sm btn-success d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-trash-restore-alt"></i>
                                            <span>Restore</span>
                                        </button>
    
                                        <button wire:click="destroyUser({{ $user->id }})"
                                            class="btn btn-sm btn-danger d-inline-flex gap-1 align-items-center">
                                            <i class="fa fa-trash-alt"></i>
                                            <span>Delete</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="10">
                                    <div class="py-1 text-center">No Other Users, Apart From You</div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10">
                                    {{ $users->links() }}
                                    @if ($users->count())
                                    <div class="text-muted">{{ $users->firstItem() }} - {{ $users->lastItem() }} out of
                                        {{ $users->total() }}</div>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modals.users.update :roles="$roles" />
    <x-modals.users.delete :name="$name" />

    @include('partials.users.bulk-roles-update')

</div>