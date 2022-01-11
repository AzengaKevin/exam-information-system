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
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="{{ count(array_keys($gradeDist)) }}">Grade Distribution</th>
                            </tr>
                            <tr>
                                @foreach (array_keys($gradeDist) as $key)
                                <th>{{ $key }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach (array_keys($gradeDist) as $key)
                                <td>{{ $gradeDist[$key] }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    drawPieChart();

    function drawPieChart() {

        let data = JSON.parse(@json(json_encode($gradeDist)));

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
                        'tomatoe'
                    ],
                    data: Object.values(data),
                    hoverOffset: 4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'left'
                    }
                }
            }
        });
    }
</script>
@endpush