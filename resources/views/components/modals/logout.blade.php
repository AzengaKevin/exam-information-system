<div id="logout-modal" class="modal fade" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Sign Out</h5>
                <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Are you sure you want to sign out of the application?</p>
            </div>
            <div class="text-white modal-footer">
                <button type="button" data-bs-dismiss="modal"
                    class="btn btn-outline-secondary">Nevermind</button>
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </div>
        </form>
    </div>
</div>