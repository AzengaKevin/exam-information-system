@extends('layouts.dashboard')

@section('title', "Upload {$exam->name} Scores")

@section('content')

<div>
    <h1 class="h4 fw-bold text-muted">Upload {{ $exam->name }} Scores</h1>
</div>
<hr>

<section>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th colspan="4">My Classes</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Level Unit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($responsibilities->count())
                @foreach ($responsibilities as $responsibility)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $responsibility->pivot->subject->name }}</td>
                    <td>{{ $responsibility->pivot->levelUnit->alias }}</td>
                    <td>
                        <div class="hstack gap-2 align-items-center">
                            <a href="{{ route('exams.scores.create', [
                                'exam' => $exam,
                                'subject' => $responsibility->pivot->subject->id,
                                'level-unit' => $responsibility->pivot->levelUnit->id,
                            ]) }}" class="btn btn-sm btn-outline-primary hstack gap-1 align-items-center">
                                <i class="fa fa-upload"></i>
                                <span>Scores</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7">
                        <div class="py-1 text-center">No Responsibility created yet</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>


@endsection

@push('scripts')
<script>
    
</script>
@endpush