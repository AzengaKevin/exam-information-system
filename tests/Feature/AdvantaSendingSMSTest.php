<?php

namespace Tests\Feature;

use App\Actions\Messages\SendAdvantaSMS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdvantaSendingSMSTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group advanta */
    public function testAnSmsCanBeSentViaAdvanta()
    {
        $this->withoutExceptionHandling();

        $result = SendAdvantaSMS::invoke([
            'phone' => '254762686021',
            'content' => $this->faker->sentence()
        ]);

        $this->assertTrue($result);
        
    }
}
