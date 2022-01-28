@extends('layouts.dashboard')

@section('title', 'Guardians')

@section('content')

<livewire:guardians :trashed="$trashed" />

@endsection

@push('scripts')
<script>
    livewire.on('show-upsert-guardian-modal', () => $('#upsert-guardian-modal').modal('show'))
    livewire.on('hide-upsert-guardian-modal', () => $('#upsert-guardian-modal').modal('hide'))

    livewire.on('show-delete-guardian-modal', () => $('#delete-guardian-modal').modal('show'))
    livewire.on('hide-delete-guardian-modal', () => $('#delete-guardian-modal').modal('hide'))

    livewire.on('hide-import-guardians-spreadsheet-modal', () => 
        $('#import-guardians-spreadsheet-modal').modal('hide'))
        
</script>
@endpush