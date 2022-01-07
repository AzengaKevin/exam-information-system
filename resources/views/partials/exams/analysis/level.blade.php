<div class="row g-4 py-3">
    <div class="col-md-12">
        <x-exams.analysis.level-line-graph :exam="$exam" :level="$level" />
    </div>
    @if ($systemSettings->school_level == 'secondary')        
    <div class="col-md-12">
        <div class="card h-100 rounded-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="my-0 h5">{{ $level->name }} Grade Distribution</h3>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-4">
                        <canvas id="level-pie-chart" width="400" height="400"></canvas>
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="{{ count(array_keys($levelGradeDistribution)) }}">Grade Distribution</th>
                                    </tr>
                                    <tr>
                                        @foreach (array_keys($levelGradeDistribution) as $key)
                                        <th>{{ $key }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach (array_keys($levelGradeDistribution) as $key)
                                        <td>{{ $levelGradeDistribution[$key] }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="{{ count(array_keys($classScores)) }}">Stream Scores</th>
                                    </tr>
                                    <tr class="text-uppercase">
                                        @foreach (array_keys($classScores) as $key)
                                        <th>{{ $key }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach (array_keys($classScores) as $key)
                                        <td>{{ $classScores[$key] }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <x-exams.analysis.level-subject-performance :exam="$exam" :level="$level" />
    </div>
</div>

@push('scripts')
<script>
    drawPieChart();

    function drawPieChart() {

        let data = JSON.parse(@json(json_encode($levelGradeDistribution)));

        let ctx = document.getElementById("level-pie-chart").getContext('2d');

        let chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: "{{ $level->name }}",
                    backgroundColor: [
                        'red',
                        'blue',
                        'green',
                        'yellow',
                        'black',
                        'purple',
                        'cyan',
                        'indigo',
                        'brown',
                        'teal',
                        'orange',
                        'pink',
                        'gray',
                        'white'
                    ],
                    data: Object.values(data),
                    hoverOffset: 4
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