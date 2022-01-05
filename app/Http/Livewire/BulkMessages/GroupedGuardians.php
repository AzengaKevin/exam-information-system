<?php

namespace App\Http\Livewire\BulkMessages;

use App\Models\Guardian;
use App\Models\Level;
use Livewire\Component;
use App\Models\LevelUnit;
use App\Models\Message;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupedGuardians extends Component
{
    public $levels;
    public $levelUnits;

    public $groupBy = 'all';
    public $selectedLevels;
    public $selectedLevelUnits;
    public $content;

    /**
     * Initialized some of the collections user
     * 
     * Called ones when the component is mounting
     */
    public function mount()
    {
        $this->levels = Level::all(['id', 'name']);
        $this->levelUnits = LevelUnit::all(['id', 'alias']);
    }

    /**
     * Renders and re-renders every time the component state changes
     */
    public function render()
    {
        return view('livewire.bulk-messages.grouped-guardians');
    }

    /**
     * Component filed hook to be called when the $groupedBy fields gets updated
     * 
     * @param mixed $grupedBy
     */
    public function updatedGroupBy($value)
    {
        $this->reset(['selectedLevels', 'selectedLevelUnits']);
    }

    /**
     * Validation rules for the quired fields
     */
    public function rules()
    {
        return [
            'groupBy' => ['bail', 'required', 'string', Rule::in(['all', 'levels', 'streams'])],
            'selectedLevels' => ['bail', 'array', 'nullable'],
            'selectedLevelUnits' => ['bail', 'array', 'nullable'],
            'content' => ['bail', 'required', 'string']
        ];
    }

    /**
     * Send use message according to guardians select
     */
    public function sendMessages()
    {
        $data = $this->validate();

        /** @var Collection */
        $userIds = collect([]);
        
        switch ($data['groupBy']) {

            case 'all':
                $userIds = User::type('guardian')->get(['id'])->pluck('id');
                break;

            case 'levels':
                $levels = array_filter($data['selectedLevels'], fn($value, $key) => boolval($value), ARRAY_FILTER_USE_BOTH);

                $userIds = DB::table('students')
                    ->select('students.id')
                    ->addSelect([
                        'recipient_id' => DB::table('student_guardians')
                        ->join('users', function ($join) {
                            $join->on('student_guardians.guardian_id', '=', 'users.authenticatable_id')
                                ->where('users.authenticatable_type', 'guardian');
                        })
                        ->select("users.id")
                        ->whereColumn('students.id', '=', 'student_guardians.student_id')
                        ->take('1')
                    ])
                    ->whereIn('level_id', array_keys($levels))
                    ->get()
                    ->pluck('recipient_id');
                break;

            case 'streams':
                $levelUnits = array_filter($data['selectedLevelUnits'], fn($value, $key) => boolval($value), ARRAY_FILTER_USE_BOTH);

                $userIds = DB::table('students')
                    ->select('students.id')
                    ->addSelect([
                        'recipient_id' => DB::table('student_guardians')
                        ->join('users', function ($join) {
                            $join->on('student_guardians.guardian_id', '=', 'users.authenticatable_id')
                                ->where('users.authenticatable_type', 'guardian');
                        })
                        ->select("users.id")
                        ->whereColumn('students.id', '=', 'student_guardians.student_id')
                        ->take('1')
                    ])
                    ->whereIn('level_unit_id', array_keys($levelUnits))
                    ->get()
                    ->pluck('recipient_id');
                break;
            
            default:
                // Just do nothing
                break;
        }

        $currentUserId = Auth::id();

        // Send the messages now
        $userIds->each(function($id) use($currentUserId, $data){
            Message::create([
                'type' => 'bulk',
                'content' => $data['content'],
                'recipient_id' => $id,
                'sender_id' => $currentUserId
            ]);
        });
        
    }

}
