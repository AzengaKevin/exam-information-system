<?php

namespace App\Http\Livewire;

use App\Services\MessageService;
use Livewire\Component;

class Messages extends Component
{

    public bool $trashed;

    /**
     * Lifecycle hook that executes once when the component is rendering
     * 
     * @param bool $trashed
     */
    public function mount(bool $trashed = false)
    {
        $this->trashed = $trashed;
    }

    /**
     * Lifecycle hook that renders the component everytime the state of the component changes
     * 
     * @param MessageService
     * @return View
     */
    public function render(MessageService $messageService)
    {
        return view('livewire.messages', [
            'messages' => $messageService->getPaginatedMessages()
        ]);
    }
}
