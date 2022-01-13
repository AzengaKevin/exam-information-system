<div>
    <!-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th colspan="4">All Levels</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @if ($levelsWithScores->count())
                @foreach ($levelsWithScores as $level)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $level->name }}</td>
                    <td>
                        @if ($level->pivot->average)
                        <span class="badge py-2 fs-6 bg-success">Published</span>
                        @else
                        <span class="badge py-2 fs-6 bg-warning">Not Published</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-inline-flex gap-2 align-items-center">

                            <a href="{{ route('exams.scores.manage', [
                                'exam' => $exam,
                                'level' => $level->id
                            ]) }}"
                                class="btn btn-sm btn-outline-primary d-inline-flex gap-1 align-items-center">
                                <i class="fa fa-cog"></i>
                                <span>Manage Level</span>
                            </a>
                        </div>

                    </td>
                </tr>
                @endforeach
                @else
                @endif
            </tbody>
        </table>
    </div>
</div>