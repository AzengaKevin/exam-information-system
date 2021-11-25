<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Active?</th>
                    <th>Profession</th>
                    <th>Location</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($guardians->count())
                @foreach ($guardians as $guardian)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($guardian->auth)->name }}</td>
                    <td>{{ optional($guardian->auth)->email }}</td>
                    <td>{{ optional($guardian->auth)->active ? 'True' : 'False' }}</td>
                    <td>{{ $guardian->profession }}</td>
                    <td>{{ $guardian->location }}</td>
                    <td>{{ $guardian->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editGuardian({{ $guardian }})"
                                class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                <i class="fa fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="8">
                        <div class="py-1 text-center">No Guardians Added Yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        {{ $guardians->links() }}
                        @if ($guardians->count())
                        <div class="text-muted">{{ $guardians->firstItem() }} - {{ $guardians->lastItem() }} out of
                            {{ $guardians->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <x-modals.guardians.upsert :guardianId="$guardianId" />

</div>