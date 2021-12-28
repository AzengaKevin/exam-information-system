@props([
'exam',
'studentScores',
'outOfs',
'subjectColumns',
'subjectsMap',
'swahiliComments',
'englishComments',
'ctComments',
'pComments',
'teachers',
'systemSettings',
'generalSettings'
])

<div>
    <div>
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
        <h2 style="margin: 0.25rem 0 0.5rem 0; font-size: 1.25rem;">{{ $exam->name }}</h2>
        <table class="w-100">
            <tbody>
                <tr>
                    <th class="text-start">NAME:</th>
                    <td class="text-left" colspan="5">{{ $studentScores->name }}</td>
                </tr>
                <tr>
                    <th class="text-start">ADMNO:</th>
                    <td class="text-left">{{ $studentScores->adm_no }}</td>
                    <th class="text-start">FORM:</th>
                    <td class="text-left">{{ $studentScores->alias }}</td>
                    <th class="text-start">HOSTEL:</th>
                    <td class="text-left">{{ $studentScores->hostel ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="font-size: 12px">
            <thead>
                <tr>
                    <th>AGGREGATES</th>
                    <th>TOTAL MARKS</th>
                    <th>MEAN MARKS</th>
                    <th>TOTAL POINTS</th>
                    <th>MEAN GRADE</th>
                    <th>OVERALL POSITION</th>
                    <th>STREAM POSITION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>VALUE</th>
                    <td>{{ $studentScores->tm ?? '-' }}</td>
                    <td>{{ $studentScores->mm ?? '-' }}{{ ($studentScores->mm) ? "%" : "" }}</td>
                    <td>{{ $studentScores->tp ?? '-' }}</td>
                    <td>{{ $studentScores->mg ?? '-' }}</td>
                    <td>{{ $studentScores->op ?? '-' }}</td>
                    <td>{{ $studentScores->sp ?? '-' }}</td>
                </tr>
                <tr>
                    <th>OUT OF</th>
                    <td>{{ $outOfs['tm'] ?? '-' }}</td>
                    <td>{{ $outOfs['mm'] ?? '-' }}%</td>
                    <td>{{ $outOfs['tp'] ?? '-' }}</td>
                    <td>{{ $outOfs['mg'] ?? '-' }}</td>
                    <td>{{ $outOfs['lsc'] ?? '-' }}</td>
                    <td>{{ $outOfs['lusc'] ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <h2 style="margin: 0.25rem 0 0.5rem 0; font-size: 1.25rem;">Breakdown</h2>

        <table class="table table-sm table-bordered table-hover border border-dark" style="font-size: 12px">
            <thead class="text-uppercase text-start">
                <tr>
                    <th><span>SUBJECT</span></th>
                    <th>MARKS</th>
                    <th><span>DEV.</span></th>
                    <th><span>GR.</span></th>
                    <th><span>RANK</span>
                    <th><span>COMMENT</span></th>
                    <th><span>TEACHER</span></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($subjectColumns as $col)
                @if (!is_null($studentScores->$col))
                <tr>
                    @php
                    $subjectScore = json_decode($studentScores->$col);
                    @endphp
                    <td class="text-uppercase">{{ $subjectsMap[$col] ?? $col }}</td>
                    <td>{{ $subjectScore->score }}%</td>
                    <td>0</td>
                    <td>{{ $subjectScore->grade }}</td>
                    <td>{{ $subjectScore->rank ?? '-' }} / {{ $subjectScore->total ?? '-' }}</td>

                    @if ($col == 'kis')
                    <td>{{ $swahiliComments[$subjectScore->grade] ?? 'Hakuna maoni' }}</td>
                    @else
                    <td>{{ $englishComments[$subjectScore->grade] ?? 'No Comments' }}</td>
                    @endif
                    <td>{{ $teachers[$col] ?? '-' }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div>

        <h2 style="margin: 0.25rem 0 0.5rem 0; font-size: 1.25rem;">Remarks</h2>

        <table style="width: 100%;">
            <thead></thead>
            <tbody>
                <tr>
                    <td class="text-start">
                        <span class="fw-bold">
                            <span>Class Teacher's Remarks</span>
                            <span  class="text-secondary"> - {{ $teachers['ct'] ?? 'N/A' }}</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="text-start" style="padding: 1rem 0 1.5rem 0;">
                        <span>{{ $ctComments[$studentScores->mg] ?? '-' }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table style="width: 100%;">
            <thead></thead>
            <tbody>
                <tr>
                    <td class="text-start">
                        <span class="fw-bold">
                            <span>Principal's Remarks</span>
                            <span  class="text-secondary"> - {{ $teachers['p'] ?? 'N/A' }}</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="text-start" style="padding: 1rem 0 1.5rem 0;">
                        <span>{{ $pComments[$studentScores->mg] ?? '-' }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>