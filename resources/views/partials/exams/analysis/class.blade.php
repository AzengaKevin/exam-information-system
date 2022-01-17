<hr>
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('exams.analysis.download', ['exam' => $exam, 'level-unit' => $levelUnit->id]) }}"
        class="btn btn-outline-primary d-inline-flex gap-2 align-items-center" download>
        <i class="fa fa-print"></i>
        <span>Download Analysis</span>
    </a>
</div>
<hr>
<div class="row g-4 py-3">
    <div class="col-md-6">
        <x-exams.analysis.level-unit-subject-performance :exam="$exam" :levelUnit="$levelUnit" />
    </div>
    <div class="col-md-6">
        <x-exams.analysis.level-unit-student-performance :exam="$exam" :levelUnit="$levelUnit" />
    </div>
    @if ($systemSettings->school_level == 'secondary')        
    <div class="col-md-12">
        <x-exams.analysis.level-unit-grade-distribution :exam="$exam" :levelUnit="$levelUnit" />
    </div>
    @endif
    <div class="col-md-6">
        <x-exams.analysis.level-unit-most-improved-students :exam="$exam" :levelUnit="$levelUnit" />
    </div>
    <div class="col-md-6">
        <x-exams.analysis.level-unit-most-dropped-students :exam="$exam" :levelUnit="$levelUnit" />
    </div>
</div>