<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($hostels->count())
                @foreach ($hostels as $hostel)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $hostel->name }}</td>
                    <td>{{ $hostel->description }}</td>
                    <td>{{ $hostel->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center justify-content-center">
                            <a href="{{route('hostels.show',['hostel'=>$hostel->slug])}}" class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </a>
                            <button wire:click="editHostel({{ $hostel }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeleteHostelModal({{ $hostel }})" class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
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
                        <div class="py-1">No Hostel created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-modals.hostels.upsert :hostelId="$hostelId" />
    <x-modals.hostels.delete :name="$name" />
    
</div>