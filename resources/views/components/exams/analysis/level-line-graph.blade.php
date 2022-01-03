<div class="card h-100 rounded-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="my-0">{{ $level->name }}</h3>

                    @if (!request()->has('level'))
                    <a href="{{ route('exams.analysis.index', ['exam' => $exam, 'level' => $level->id]) }}"
                        class="btn btn-sm btn-outline-primary hstack gap-2 align-items-center">
                        <i class="fa fa-eye"></i>
                        <span>Details</span>
                    </a>
                    @endif
                </div>
            </div>
            <hr>
            <div class="col-md-6">
                <canvas id="level-{{ $level->id }}-chart" width="600" height="200"></canvas>
            </div>

            @if ($systemSettings->school_level == 'secondary')                
            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <h6 class="text-secondary">Mean Points</h6>
                    <span class="text-success fw-bolder display-6">{{ $levelWithData->pivot->points ?? '-' }}</span>
                    <span class="text-secondary fw-bold">+.0054</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <h6 class="text-secondary">Mean Grade</h6>
                    <span class="text-success fw-bolder display-6">{{ $levelWithData->pivot->grade ?? '-' }}</span>
                </div>
            </div>
            @else                
            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <h6 class="text-secondary">Average</h6>
                    <span class="text-success fw-bolder display-6">{{ $levelWithData->pivot->average ?? '-' }}</span>
                    <span class="text-secondary fw-bold">5.4</span>
                </div>
            </div>
            @endif
            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <h6 class="text-secondary">Students</h6>
                    <span class="text-success fw-bolder display-6">{{ $studentsCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    drawChart();

    function drawChart() {

        let data = JSON.parse(@json(json_encode($levelUnitsPointsData)));

        let ctx = document.getElementById("level-{{ $level->id }}-chart").getContext('2d');

        let chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: "{{ $level->name }}",
                    backgroundColor: '#adb5bd',
                    borderColor: '#0d6efd',
                    data: Object.values(data),
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
@endpush