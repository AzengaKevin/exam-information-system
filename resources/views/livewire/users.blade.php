<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Active?</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($users->count())
                @foreach ($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->authenticatable_type ?? 'Anonymous') }}</td>
                    <td>{{ $user->active ? 'True' : 'False' }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editUser({{ $user }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="toggleUserActiveStatus({{ $user }})" class="btn btn-sm btn-outline-{{ $user->active ? 'warning' : 'success' }} hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>{{ $user->active ? 'Deativate' : 'Activate' }}</span>
                            </button>
                            <button wire:click="showDeleteUserModal({{ $user }})" class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                <i class="fa fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7">
                        <div class="py-1">No Other Users, Apart From You</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
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

    <x-modals.users.update :roles="$roles" />
    <x-modals.users.delete :name="$name" />
    
</div>