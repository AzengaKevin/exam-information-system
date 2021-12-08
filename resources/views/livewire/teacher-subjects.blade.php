<div>
    <x-feedback />
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center">Classes</th>
                </tr>
            </thead>
            <tbody>
                @if ($teacherSubjects->count())
                @foreach ($teacherSubjects as $subject)
                <tr>
                    <td>{{ $subject->name }}</td>
                    <td class="text-center">{{ $subject->classes ?? 0 }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="2">No Subjects Assigned Yet</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-modals.teachers.subjects.update :subjects="$subjects" :name="$teacher->auth->name" />
</div>