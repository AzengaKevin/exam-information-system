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
            <hr style="height: .5px; background-color: black; margin-top: 1px;">
        </div>
        <h2 style="margin: 0.25rem 0 0.5rem 0; font-size: 1.25rem;">{{ $exam->name }}</h2>
        <div class="table-responsive">
            <table class="w-100">
                <tbody>
                    <tr>
                        <th class="text-start">NAME:</th>
                        <td class="text-left">{{ $studentScores->name }}</td>

                        @if ($systemSettings->school_level === 'secondary')
                        <th class="text-start">ADMNO:</th>
                        <td class="text-left">{{ $studentScores->adm_no }}</td>
                        @endif
                        @if ($systemSettings->school_level === 'secondary')
                        <th class="text-start">FORM:</th>
                        @else
                        <th class="text-start">CLASS:</th>
                        @endif
                        <td class="text-left">{{ $studentScores->alias ?? $studentScores->level }}</td>
                        @if ($systemSettings->boarding_school)
                        <th class="text-start">HOSTEL:</th>
                        <td class="text-left">{{ $studentScores->hostel ?? 'N/A' }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive">
            <table class="table" style="font-size: 12px">
                <thead>
                    <tr>
                        <th>AGGREGATES</th>
                        <th>TOTAL MARKS</th>
                        <th>MEAN MARKS</th>
                        @if ($systemSettings->school_level == 'secondary')
                        <th>TOTAL POINTS</th>
                        <th>MEAN GRADE</th>
                        @endif
                        <th>OVERALL POSITION</th>
                        @if ($systemSettings->school_has_streams)
                        <th>STREAM POSITION</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>VALUE</th>
                        <td>{{ $studentScores->tm ?? '-' }}</td>
                        <td>{{ $studentScores->mm ?? '-' }}{{ ($studentScores->mm) ? "%" : "" }}</td>
                        @if ($systemSettings->school_level == 'secondary')
                        <td>{{ $studentScores->tp ?? '-' }}</td>
                        <td>{{ $studentScores->mg ?? '-' }}</td>
                        @endif
                        <td>{{ $studentScores->op ?? '-' }}</td>
                        @if ($systemSettings->school_has_streams)
                        <td>{{ $studentScores->sp ?? '-' }}</td>
                        @endif
                    </tr>
                    <tr>
                        <th>OUT OF</th>
                        <td>{{ $outOfs['tm'] ?? '-' }}</td>
                        <td>{{ $outOfs['mm'] ?? '-' }}%</td>

                        @if ($systemSettings->school_level == 'secondary')
                        <td>{{ $outOfs['tp'] ?? '-' }}</td>
                        <td>{{ $outOfs['mg'] ?? '-' }}</td>
                        @endif
                        <td>{{ $outOfs['lsc'] ?? '-' }}</td>
                        @if ($systemSettings->school_has_streams)
                        <td>{{ $outOfs['lusc'] ?? '-' }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 style="margin: 0.25rem 0 0.5rem 0; font-size: 1.25rem;">Breakdown</h2>

        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover border border-dark" style="font-size: 12px">
                <thead class="text-uppercase text-start">
                    <tr>
                        <th><span>SUBJECT</span></th>
                        <th>MARKS</th>
                        <th><span>DEV.</span></th>

                        @if ($systemSettings->school_level == 'secondary')
                        <th><span>GR.</span></th>
                        @endif
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
                        @if ($systemSettings->school_level == 'secondary')
                        <td>{{ $subjectScore->grade }}</td>
                        @endif
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
                            <span class="text-secondary"> - {{ $teachers['ct'] ?? 'N/A' }}</span>
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
                            @if ($systemSettings->school_level === 'secondary')
                            <span>Principal's Remarks</span>
                            @else
                            <span>Head Teacher's Remarks</span>
                            @endif
                            <span class="text-secondary"> - {{ $teachers['p'] ?? 'N/A' }}</span>
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