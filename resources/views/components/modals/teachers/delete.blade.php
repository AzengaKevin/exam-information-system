@props(['name'])

<div wire:ignore.self id="delete-teacher-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="delete-teacher-modal-title">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="delete-teacher-modal-title" class="modal-title">Delete Teacher Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead">Are you sure you want to delete the teacher, <strong>{{$name}}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                <button type="submit" wire:click="deleteTeacher" class="btn btn-outline-danger">Confirm</button>
            </div>
        </div>
    </div>
</div>