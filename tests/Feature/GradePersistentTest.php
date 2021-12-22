<?php

namespace Tests\Feature;

use App\Models\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GradePersistentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group grades */
    public function testAGradeEntryToTheDatabaseCanBeMade()
    {
        $this->withoutExceptionHandling();

        $paylod = Grade::factory()->make()->toArray();

        $grade = Grade::create($paylod);

        $this->assertNotNull($grade);

        $this->assertEquals($paylod['grade'], $grade->grade);

        $this->assertEquals($paylod['points'], $grade->points);

        $this->assertEquals($paylod['english_comment'], $grade->english_comment);

        $this->assertEquals($paylod['swahili_comment'], $grade->swahili_comment);

        $this->assertEquals($paylod['ct_comment'], $grade->ct_comment);
        
        $this->assertEquals($paylod['p_comment'], $grade->p_comment);
        
    }

}
