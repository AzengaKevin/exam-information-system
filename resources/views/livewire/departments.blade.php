<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($departments->count())
                @foreach ($departments as $department)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->description }}</td>
                    <td>{{ $department->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editDepartment({{ $department }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeleteDepartmentModal({{ $department }})" class="btn btn-outline-danger hstack gap-2 align-items-center">
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
                        <div class="py-1">No Department created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            {{-- <tfoot>
                <tr>
                    <td colspan="5">
                        {{ $users->links() }}
                        @if ($users->count())
                        <div class="text-muted">{{ $users->firstItem() }} - {{ $users->lastItem() }} out of
                            {{ $users->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot> --}}
        </table>
    </div>

    <x-modals.departments.upsert :departmentId="$departmentId" />
    <x-modals.departments.delete :name="$name" />
    
</div>