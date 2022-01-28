@extends('layouts.dashboard')

@section('title', $exam->name)

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Activities</li>
        </ol>
    </nav>
</div>
<div class="row g-4 py-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Action</th>
                                <th>When</th>
                                <th>Level</th>
                                <th>Stream</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($users->count())
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->pivot->action }}</td>
                                        <td>{{ $user->pivot->created_at->diffForHumans() }}</td>
                                        <td>{{ $user->pivot->level->name }}</td>
                                        <td>{{ $user->pivot->levelUnit->alias }}</td>
                                        <td>{{ $user->pivot->subject->name }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">No Exam User Activities Recorded Yet</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    {{ $users->links() }}
                                    @if ($users->count())
                                    <div class="text-muted">{{ $users->firstItem() }} - {{ $users->lastItem() }} out of
                                        {{ $users->total() }}</div>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-exam-grades-modal', () => $('#upsert-exam-grades-modal').modal('show'))
    livewire.on('hide-upsert-exam-grades-modal', () => $('#upsert-exam-grades-modal').modal('hide'))

    livewire.on('show-delete-exam-grades-modal', () => $('#delete-exam-grades-modal').modal('show'))
    livewire.on('hide-delete-exam-grades-modal', () => $('#delete-exam-grades-modal').modal('hide'))

    livewire.on('hide-change-exam-status-modal', () => $('#change-status-exam-modal').modal('hide'))

    livewire.on('hide-update-scores-table-modal', () => $('#update-scores-table-modal').modal('hide'))
</script>
@endpush