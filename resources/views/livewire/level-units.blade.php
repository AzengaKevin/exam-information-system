<div>
    <x-feedback />

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Level</th>
                    <th>Stream</th>
                    <th>Alias</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($levelUnits->count())
                @foreach ($levelUnits as $levelUnit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($levelUnit->level)->name }}</td>
                    <td>{{ optional($levelUnit->stream)->name }}</td>
                    <td>{{ $levelUnit->alias ?? 'Not Set' }}</td>
                    <td>{{ $levelUnit->description }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <button wire:click="editLevelUnit({{ $levelUnit }})" class="btn btn-sm btn-outline-info hstack gap-1 align-items-center">
                                <i class="fa fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button  class="btn btn-sm btn-outline-danger hstack gap-2 align-items-center">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6">
                        <div class="py-1 text-center">No Level Units created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        {{ $levelUnits->links() }}
                        @if ($levelUnits->count())
                        <div class="text-muted">{{ $levelUnits->firstItem() }} - {{ $levelUnits->lastItem() }} out of
                            {{ $levelUnits->total() }}</div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <x-modals.level-units.upsert 
        :levelUnitId="$levelUnitId"
        :levels="$levels"
        :streams="$streams" />
        
    {{-- <x-modals.levels.delete :name="$name" /> --}}
    
</div>