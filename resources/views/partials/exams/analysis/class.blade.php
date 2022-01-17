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