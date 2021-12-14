<div wire:ignore.self id="subject-teachers-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="subject-teachers-modal-title">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="subject-teachers-modal-title" class="modal-title">{{ $name }} Teachers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <th>Name</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if ($teachers->count())           
                            @foreach ($teachers as $teacher)
                            <tr>
                                <td>{{ $teacher->auth->name }}</td>
                                <td>
                                    <a class="btn btn-sm btn-primary d-inline-flex gap-2 align-items-center" href="{{ route('teachers.show', $teacher) }}">
                                        <i class="fa fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="2">Subject has no teachers</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>