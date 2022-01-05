<?php

namespace App\Http\Livewire\BulkMessages;

use App\Models\Message;
use App\Models\Student;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Livewire\WithPagination;

class RandomizedGuardians extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selectedStudents;
    public $content;

    public function render()
    {
        return view('livewire.bulk-messages.randomized-guardians', [
            'students' => $this->getPaginatedStudents()
        ]);
    }

    /**
     * Get paginated students from the database whose parent are going to be 
     * sent a messate
     * 
     * @return Collection
     */
    public function getPaginatedStudents()
    {
        return Student::orderBy('name')->paginate(48, ['id', 'name'], 'student');
    }

    /**
     * Modelled fields validations rules
     */
    public function rules()
    {
        return [
            'selectedStudents' => ['array', 'nullable', 'min:1'],
            'content' => ['bail', 'required', 'string']
        ];
    }

    /**
     * Sending the created message to the users now
     */
    public function sendMessages()
    {
        $data = $this->validate();

        $students = array_filter($data['selectedStudents'], fn($value, $key) => boolval($value), ARRAY_FILTER_USE_BOTH);

        /** @var Collection */
        $userIds = DB::table('students')
            ->select("students.id")
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
            ->whereIn('students.id', array_keys($students))
            ->get()
            ->pluck('recipient_id');

        $currentUserId = Auth::id();

        // Send the messages now
        $userIds->each(function($id) use($currentUserId, $data){

            if(!is_null($id)){
                Message::create([
                    'type' => 'bulk',
                    'content' => $data['content'],
                    'recipient_id' => $id,
                    'sender_id' => $currentUserId
                ]);
            }
        });            
        
    }
}
