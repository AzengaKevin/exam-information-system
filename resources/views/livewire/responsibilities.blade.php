<div>
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
                            <th>Created</th>
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
                            <td>{{ $responsibility->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-inline-flex gap-2 align-items-center">
                                    <button wire:click="editResponsibility({{ $responsibility }})"
                                        class="btn btn-sm btn-outline-info d-inline-flex gap-1 align-items-center">
                                        <i class="fa fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
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
                                    <button wire:click="showDeleteResponsibilityModal({{ $responsibility }})"
                                        class="btn btn-sm btn-outline-danger d-inline-flex gap-2 align-items-center">
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
                                <div class="py-1">No Responsibility created yet</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.responsibilities.upsert :responsibilityId="$responsibilityId" 
        :requirementOptions="$requirementOptions" />
    <x-modals.responsibilities.delete :name="$name" />

</div>