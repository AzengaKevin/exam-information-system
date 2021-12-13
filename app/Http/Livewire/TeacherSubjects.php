<?php

namespace App\Http\Livewire;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class TeacherSubjects extends Component
{
    public Teacher $teacher;

    public $selectedSubjects;

    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;

        $this->updateTeacherSubjects();
    }

    public function render()
    {
        return view('livewire.teacher-subjects', [
            'subjects' => $this->getAllSubjects(),
            'teacherSubjects' => $this->getTeacherSubjects()
        ]);
    }

    public function getTeacherSubjects()
    {
        return $this->teacher->fresh()->subjects()
            ->addSelect(['subject_classes_count' => DB::table('responsibility_teacher')
                ->selectRaw("COUNT(responsibility_teacher.subject_id)")
                ->whereColumn('subjects.id', 'responsibility_teacher.subject_id')
                ->where('responsibility_teacher.teacher_id', $this->teacher->id)
                ->take(1)
            ])
            ->get();
    }

    public function getAllSubjects()
    {
        return Subject::all(['id', 'name']);
    }

    public function updateTeacherSubjects()
    {
        foreach ($this->teacher->subjects->pluck('id')->toArray() as $subject) {
            $this->selectedSubjects[$subject] = 'true';
        }
    }

    public function updateSubjects()
    {

        $data = $this->validate([
            'selectedSubjects' => ['required', 'array']
        ]);

        $payload = array_filter($data['selectedSubjects'], function($value, $key){
            return $value == 'true';
        }, ARRAY_FILTER_USE_BOTH);

        try {

            $this->teacher->subjects()->sync(array_keys($payload));

            $this->reset(['selectedSubjects']);

            $this->updateTeacherSubjects();

            session()->flash('status', 'Teacher subjects updated');

            $this->emit('hide-update-teacher-subjects-modal');
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'teacher-id' => $this->teacher->id,
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal db error occurred');

            $this->emit('hide-update-teacher-subjects-modal');
            
        }
        
    }
}
