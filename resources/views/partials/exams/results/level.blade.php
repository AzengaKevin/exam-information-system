<div class="row g-4">
    @foreach ($level->levelUnits as $levelUnit)
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <livewire:level-unit-exam-results :exam="$exam" :levelUnit="$levelUnit" />
            </div>
        </div>
    </div>
    @endforeach
</div>