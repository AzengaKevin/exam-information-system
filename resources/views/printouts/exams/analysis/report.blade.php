<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        table.table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        table.table>thead {
            font-weight: bold;
        }

        table.table>thead>tr {
            border: 1px solid black;
        }

        table.table>thead>tr>th {
            border: 1px solid rgb(110, 52, 52);
            padding: .5rem;
        }

        table.table>tbody {
            border: 1px solid black;
        }

        table.table>tbody>tr {
            border: 1px solid black;
        }

        table.table>tbody>tr>td {
            border: 1px solid black;
            padding: .3rem;
        }

        table.table>tbody>tr>th {
            border: 1px solid black;
            padding: .3rem;
        }


        .text-center {
            text-align: center !important;
        }

        .fw-bold {
            font-weight: bold;
        }

        .d-table .d-table-row .d-table-cell {
            vertical-align: top;
        }

        .d-table {
            display: table;
        }

        .d-table-row {
            display: table-row;
        }

        .d-table-cell {
            display: table-cell;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .text-start {
            text-align: left !important;
        }

        .w-25 {
            width: 25%;
        }

        .w-75 {
            width: 75%;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        .fw-normal {
            font-weight: normal !important;
        }

        .fw-bold {
            font-weight: bold !important;
        }

        .w-1\/3 {
            width:
        }

        .mx-auto {
            margin: 0 auto;
        }

        .px-2 {
            padding: 2rem;
        }

        .h3 {
            font-size: 1.5rem;
        }

        .page-break {
            page-break-after: always;
        }

        .text-secondary {
            color: gray;
        }

        .mt-3 {
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>
    <div>
        <div>
            <div class="table-responsive fw-bold">
                <table>
                    <tbody>
                        <tr>
                            @if ($generalSettings->logo)
                            <td>
                                <img width="96" src="{{ $generalSettings->logo }}"
                                    alt="{{ $systemSettings->school_name }}">
                            </td>
                            @endif
                            <td>
                                <h1 class="fw-bold">{{ $systemSettings->school_name }}</h1>
                                <div class="fw-bold">{{ $generalSettings->school_address }}</div>
                                <div class="fw-bold">Tel: {{ $generalSettings->school_telephone_number }}</div>
                                <div class="fw-bold">{{ $generalSettings->school_email_address }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr style="height: .5px; background-color: black; margin-bottom: 0px;">
        </div>
        <div class="mt-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="{{ ($systemSettings->school_level == 'secondary') ? 6 : 4}}">{{ $level->name }}
                                Exam Analysis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @if ($systemSettings->school_level == 'secondary')
                            <th>MEAN POINTS</th>
                            <td>{{ $levelWithData->pivot->points }}</td>
                            <th>MEAN GRADE</th>
                            <td>{{ $levelWithData->pivot->grade }}</td>
                            @else
                            <th>AVERAGE</th>
                            <td>{{ $levelWithData->pivot->average }}</td>
                            @endif
                            <th>STUDENTS</th>
                            <td>{{ $studentsCount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if ($systemSettings->school_level == 'secondary')
        <div class="mt-3">
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
        @endif
        <div class="mt-3">
            <x-exams.analysis.level-subject-performance :exam="$exam" :level="$level" />
        </div>
        <div class="mt-3">
            <x-exams.analysis.level-student-performance :exam="$exam" :level="$level" />
        </div>
        @if ($systemSettings->school_has_streams)
        <div class="mt-3">
            <x-exams.analysis.level-unit-performance :exam="$exam" :level="$level" />
        </div>
        @endif
    </div>
</body>

</html>