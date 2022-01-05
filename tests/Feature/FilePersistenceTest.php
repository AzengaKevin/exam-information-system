<?php

namespace Tests\Feature;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilePersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group files */
    public function testAUserCanPersistAFileWithJustThePath()
    {
        $this->withoutExceptionHandling();

        $payload = File::factory()->make()->toArray();

        /** @var File */
        $file = File::create($payload);

        $this->assertNotNull($file);

        $this->assertEquals($payload['path'], $file->path);
        
    }
}
