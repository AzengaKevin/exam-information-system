@extends('layouts.dashboard')

@section('title', "{$student->name}'s Subjects")

@section('content')

<livewire:student-subjects :student="$student" />

@endsection

@push('scripts')
<script>
    
</script>
@endpush