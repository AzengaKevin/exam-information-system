<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessagePersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group messages */
    public function testAMessageCanBePesistedToTheDatabase()
    {
        $this->withoutExceptionHandling();
        
        Notification::fake();

        $payload = Message::factory()->make()->toArray();

        $message = Message::create($payload);

        $this->assertNotNull($message);

        $this->assertEquals($payload['sender_id'], $message->sender_id);

        $this->assertEquals($payload['recipient_id'], $message->recipient_id);

        $this->assertEquals($payload['content'], $message->content);
        
    }

    /** @group messages */
    public function testAnExamMessageCanBePersisted()
    {
        $this->withExceptionHandling();

        Notification::fake();

        /** @var Exam */
        $exam = Exam::factory()->create();

        $payload = Message::factory()->make([
            'exam_id' => $exam->id
        ])->toArray();

        /** @var Message */
        $message = Message::create($payload);

        $this->assertNotNull($message);

        $this->assertEquals($payload['sender_id'], $message->sender_id);

        $this->assertEquals($payload['recipient_id'], $message->recipient_id);

        $this->assertEquals($payload['content'], $message->content);

        $this->assertEquals($payload['type'], $message->type);
    }
}
