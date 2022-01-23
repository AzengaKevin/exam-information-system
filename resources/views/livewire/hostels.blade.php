<div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if ($trashed)
                <li class="breadcrumb-item"><a href="{{ route('hostels.index') }}">Hostels</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trashed</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Hostels</li>
                @endif
            </ol>
        </nav>

        <div class="d-inline-flex flex-wrap gap-2 align-items-md-center">
            @can('create', \App\Models\Hostel::class)
            <button data-bs-toggle="modal" data-bs-target="#upsert-hostel-modal"
                class="d-md-none btn btn-outline-primary rounded-circle">
                <i class="fa fa-plus"></i>
            </button>
            <button data-bs-toggle="modal" data-bs-target="#upsert-hostel-modal"
                class="d-none d-md-inline-flex btn btn-outline-primary gap-2 align-items-center">
                <i class="fa fa-plus"></i>
                <span>Hostel</span>
            </button>
            @endcan
            @if (!$trashed)
            @can('viewTrashed', \App\Models\Hostel::class)
            <a href="{{ route('hostels.index', ['trashed' => true]) }}"
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
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Students</th>
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
                            <td>{{ $hostel->students->count() }}</td>
                            <td>{{ $hostel->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="hstack gap-2 align-items-center justify-content-center">
                                    @if (!$trashed)
                                    @can('view', $hostel)
                                    <a href="{{route('hostels.show',['hostel'=>$hostel->slug])}}"
                                        class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                    @endcan
                                    <button wire:click="editHostel({{ $hostel }})"
                                        class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button wire:click="showDeleteHostelModal({{ $hostel }})"
                                        class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <span>Delete</span>
                                    </button>
                                    @else
                                    <button wire:click="restoreHostel({{ $hostel->id }})"
                                        class="btn btn-sm btn-success hstack gap-1 align-items-center">
                                        <i class="fa fa-trash-restore-alt"></i>
                                        <span>Restore</span>
                                    </button>
                                    <button wire:click="destroyHostel({{ $hostel->id }})"
                                        class="btn btn-sm btn-danger hstack gap-1 align-items-center">
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
                            <td colspan="5">
                                <div class="py-1">No hostel {{ $trashed ? "trashed" : "created" }} yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.hostels.upsert :hostelId="$hostelId" />
    <x-modals.hostels.delete :name="$name" />

</div>