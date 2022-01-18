<hr>
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('exams.analysis.download', ['exam' => $exam, 'level' => $level->id]) }}"
        class="btn btn-outline-primary d-inline-flex gap-2 align-items-center" download>
        <i class="fa fa-print"></i>
        <span>Download Analysis</span>
    </a>
</div>
<hr>
<div class="row g-4 py-3">
    <div class="col-md-12">
        <x-exams.analysis.level-line-graph :exam="$exam" :level="$level" />
    </div>
    @if ($systemSettings->school_level == 'secondary')
    <div class="col-md-12">
        <x-exams.analysis.level-grade-distribution :exam="$exam" :level="$level" />
    </div>
    @endif
    <div class="col-md-6">
        <x-exams.analysis.level-subject-performance :exam="$exam" :level="$level" />
    </div>
    @if ($systemSettings->school_has_streams)
    <div class="col-md-6">
        <x-exams.analysis.level-unit-performance :exam="$exam" :level="$level" />
    </div>
    @endif
    <div class="col-md-6">
        <x-exams.analysis.level-student-performance :exam="$exam" :level="$level" />
    </div>
    <div class="col-md-6">
        <x-exams.analysis.level-most-improved-students :exam="$exam" :level="$level" />
    </div>
    <div class="col-md-6">
        <x-exams.analysis.level-most-dropped-students :exam="$exam" :level="$level" />
    </div>
    @if ($systemSettings->school_has_streams)
    <hr>
    @foreach ($exam->subjects as $subject)
    <div class="col-md-6">
        <x-exams.analysis.level-streams-subject-rank :exam="$exam" :level="$level" :subject="$subject" />
    </div>
    @endforeach
    @endif
    <hr>
    @foreach($exam->subjects as $subject)
    <div class="col-md-4">
        <x-exams.analysis.level-top-students-in-subject :exam="$exam" :level="$level" :subject="$subject" />
    </div>
    @endforeach
</div>