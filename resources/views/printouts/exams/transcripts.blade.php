<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $exam->name }} - {{ $levelUnit->alias }} - Transcripts</title>
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
    </style>
</head>

<body>
    <div class="container">
        @foreach ($studentsScores as $studentScores)
        <x-exams.transcript :exam="$exam" :studentScores="$studentScores" :outOfs="$outOfs"
            :subjectColumns="$subjectColumns" :subjectsMap="$subjectsMap" :swahiliComments="$swahiliComments"
            :ctComments="$ctComments" :pComments="$pComments" :englishComments="$englishComments"
            :teachers="$teachers" />

        @if (!$loop->last)
        <div class="page-break"></div>
        @endif
        @endforeach
    </div>
</body>

</html>