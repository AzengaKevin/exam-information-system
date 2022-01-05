@extends('layouts.dashboard')

@section('title', 'Messages')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-md-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Messages</li>
        </ol>
    </nav>
    <div class="d-inline-flex flex-wrap gap-2">
        <button data-bs-toggle="modal" data-bs-target="#upsert-message-modal"
            class="btn btn-outline-primary d-inline-flex gap-1 align-items-center">
            <i class="fa fa-paper-plane"></i>
            <span>Message</span>
        </button>
        <button data-bs-toggle="modal" data-bs-target="#send-bulk-sms"
            class="btn btn-outline-primary d-inline-flex gap-1 align-items-center">
            <i class="fa fa-paper-plane"></i>
            <span>Bulk Messages</span>
        </button>
    </div>
</div>
<hr>

<livewire:user-messages :user="Auth::user()" />
<livewire:bulk-messages.grouped-guardians />
<livewire:bulk-messages.randomized-guardians />

@endsection

@push('scripts')
<script>
    livewire.on('hide-upsert-message-modal', () => $('#upsert-message-modal').modal('hide'));
</script>
@endpush

@push('modals')
<div id="send-bulk-sms" class="modal fade" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div id="bulk-sms-accordion" class="accordion accordion-flush">
                    <div class="accordion-item bg-white">
                        <h2 class="accordion-header bg-white" id="teacher-heading">
                            <button type="button" class="accordion-button bg-white" data-bs-toggle="collapse"
                                data-bs-target="#teacher-collapse" aria-expanded="false"
                                aria-controls="teacher-collapse">Teachers</button>
                        </h2>
                        <div id="teacher-collapse" class="accordion-collapse collapse" aria-labelledby="teacher-heading"
                            data-bs-parent="#bulk-sms-accordion">
                            <div class="accordion-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-primary">Grouped Teachers</button>
                                    <button class="btn btn-primary">Randomized Teachers</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-white">
                        <h2 class="accordion-header bg-white" id="guardian-heading">
                            <button type="button" class="accordion-button bg-white" data-bs-toggle="collapse"
                                data-bs-target="#guardian-collapse" aria-expanded="false"
                                aria-controls="guardian-collapse">Guardians</button>
                        </h2>
                        <div id="guardian-collapse" class="accordion-collapse collapse"
                            aria-labelledby="guardian-heading" data-bs-parent="#bulk-sms-accordion">
                            <div class="accordion-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#grouped-guardians-bulk-sms-modal"
                                        class="btn btn-primary">Grouped Guardians</button>
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#randomized-guardians-bulk-sms-modal"
                                        class="btn btn-primary">Randomized Guardians</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-outline-primary">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endpush