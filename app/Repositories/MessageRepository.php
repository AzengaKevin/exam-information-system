<?php

namespace App\Repositories;

use App\Models\Message;

class MessageRepository
{
    /**
     * Retrieve all messages from the database but paginated
     * 
     * @return Paginator
     */
    public function findPaginatedMessages()
    {
        $messageQuery = Message::latest();

        return $messageQuery->paginate(Message::PAGE_SIZE)->withQueryString();
    }
}
