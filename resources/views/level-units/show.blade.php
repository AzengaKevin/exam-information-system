@extends('layouts.dashboard')

@section('title', $levelUnit->level->name ." ".$levelUnit->stream->name)

@section('content')

<div class="d-flex justify-content-between">
    <h1 class="h4 fw-bold text-muted">{{$levelUnit->level->name}} {{$levelUnit->stream->name}}</h1>
    <button data-bs-toggle="modal" data-bs-target="#upsert-level-unit-student-modal" class="btn btn-outline-primary hstack gap-2 align-items-center">
        <i class="fa fa-plus"></i>
        <span>Upgrade Students</span>
    </button>
</div>
<hr>

@livewire('level-unit-students',['levelUnit'=>$levelUnit])

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-level-unit-student-modal', () => $('#upsert-level-unit-student-modal').modal('show'))
    livewire.on('hide-upsert-level-unit-student-modal', () => $('#upsert-level-unit-student-modal').modal('hide'))

    livewire.on('show-delete-level-unit-student-modal', () => $('#delete-level-unit-student-modal').modal('show'))
    livewire.on('hide-delete-level-unit-student-modal', () => $('#delete-level-unit-student-modal').modal('hide'))
</script>
@endpush