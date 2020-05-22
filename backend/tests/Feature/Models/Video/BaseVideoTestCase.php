<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class BaseVideoTestCase extends TestCase
{
    use DatabaseMigrations;
    protected $data;
    protected $fileFields;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => 'Test Video Test',
            'description' => 'Testes phpunit',
            'year_launched' => 2019,
            'opened' => false,
            'rating' => Video::RATING_LIST[0],
            'duration' => rand(30, 60)
        ];
        $fileFields = [];
        foreach (Video::$fileFields as $fil){
            $fileFields[$fil] = "$fil.test";
        }

        $this->fileFields = $fileFields;
    }
}