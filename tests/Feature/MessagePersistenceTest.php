<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessagePersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group messages */
    public function testAMessageCanBePesistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Message::factory()->make()->toArray();

        $message = Message::create($payload);

        $this->assertNotNull($message);

        $this->assertEquals($payload['sender_id'], $message->sender_id);

        $this->assertEquals($payload['recipient_id'], $message->recipient_id);

        $this->assertEquals($payload['content'], $message->content);
        
    }
}
