<?php

namespace App\Http\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class UserMessages extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public User $user;

    public $recipient_id;
    public $content;

    public $messageId;

    /**
     * The first method to be called when mounting the component
     */
    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.user-messages', [
            'messages' => $this->getAllUserMessages(),
            'users' => $this->getAllPossibleRecipients()
        ]);
    }

    /**
     * Get the sent and received messages for the currently logged in user
     */
    public function getAllUserMessages()
    {
        return Message::with(['sender', 'recipient'])->for($this->user->fresh())->latest()->paginate(24)->withQueryString();
    }

    /**
     * Get all users from the database apart from the currents authenticated user,
     * since no one is allowed to send a message to themselves
     */
    public function getAllPossibleRecipients()
    {
        return User::where('id', '!=', Auth::id())->get(['id', 'name']);
        
    }

    /**
     * Sending a message validation rules
     */
    public function rules()
    {
        return [
            'recipient_id' => ['required', 'integer'],
            'content' => ['bail', 'required', 'string']
        ];
    }

    /**
     * Persists a new message to the database
     */
    public function createMessage()
    {
        $data = $this->validate();

        /** @var User */
        $currentUser = Auth::user();

        try {

            $message = $currentUser->messages()->create($data);

            if ($message) {
                
                $this->reset(['recipient_id', 'content']);

                session()->flash('status', 'Message sent');

                $this->emit('hide-upsert-message-modal');
            }
            
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('status', 'Message sending failed');

            $this->emit('hide-upsert-message-modal');
            
        }
        
    }

    /**
     * Updates a database message record
     */
    public function updateMessage()
    {
        
    }
}
