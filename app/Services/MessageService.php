<?php

namespace App\Services;

use App\Repositories\MessageRepository;

class MessageService
{
    private MessageRepository $messageRepository;

    /**
     * Creates a MessageService instance
     * 
     * @param MessageRepository
     * @return void
     */
    public function __construct(MessageRepository $messageRepository) {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Get application message in the current page
     * 
     * @return Paginator
     */
    public function getPaginatedMessages()
    {
        return $this->messageRepository->findPaginatedMessages();
    }
}
