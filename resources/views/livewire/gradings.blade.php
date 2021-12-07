<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <th>#</th>
                <th>Name</th>
                <th>Updated?</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @if ($gradings->count())
                @foreach ($gradings as $grading)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $grading->name }}</td>
                    <td>{{ $grading->updated_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Details</span>
                            </button>
                            <button wire:click="editGrading({{ $grading }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-eye"></i>
                                <span>Edit</span>
                            </button>
                            <button wire:click="showDeleteGradingModal({{ $grading }})" class="btn btn-sm btn-outline-danger hstack gap-1 align-items-center">
                                <i class="fa fa-trash-alt"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4">
                        <div class="py-1 text-center">No Grading System has been added yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-modals.gradings.upsert :gradingId="$gradingId" :grades="$grades" :values="$values" />
    <x-modals.gradings.delete :name="$name" />
</div>