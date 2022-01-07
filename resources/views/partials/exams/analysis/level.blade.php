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
    <div class="col-md-6">
        <x-exams.analysis.level-student-performance :exam="$exam" :level="$level" />
    </div>
    @if ($systemSettings->school_has_streams)        
    <div class="col-md-6">
        <x-exams.analysis.level-unit-performance :exam="$exam" :level="$level" />
    </div>
    @endif
</div>