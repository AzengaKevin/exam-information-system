<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $level->name ?? $levelUnit->alias }} - {{ $exam->name }} Transacript</title>
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

        .text-uppercase{
            text-transform: uppercase;
        }
        
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="text-center">
            <div class="fw-bold">
                <h1 class="fw-bold">{{ $systemSettings->school_name }}</h1>
                <div class="fw-bold">{{ $generalSettings->school_address }}</div>
                <div class="fw-bold">Tel: {{ $generalSettings->school_telephone_number }}</div>
                <div class="fw-bold">{{ $generalSettings->school_email_address }}</div>
                <hr style="height: 2px; background-color: black; margin-bottom: 0px;">
                <hr style="height: .5px; background-color: black; margin-top: 1px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="text-uppercase">
                    <tr class="text-center">
                        <th colspan="{{ count($cols) }}">{{ $level->name ?? $levelUnit->alias }} - {{ $exam->name }} Results</th>
                    </tr>
                    <tr>
                        @foreach ($cols as $col)
                        <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if ($data->count())
                    @foreach ($data as $item)
                    <tr>
                        @foreach ($cols as $col)
                        @if (in_array($col, $subjectCols))
                        @php
                        $score = json_decode($item->$col);
                        @endphp
                        <td>
                            <span>{{ optional($score)->score ?? null }}</span>
                            @if ($systemSettings->school_level == 'secondary')
                            <span>{{ optional($score)->grade ?? null }}</span>
                            @endif
                        </td>
                        @else
                        <td>{{ $item->$col }}</td>
                        @endif
                        @endforeach
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>