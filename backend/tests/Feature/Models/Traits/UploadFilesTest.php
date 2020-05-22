<?php

namespace Tests\Feature\Models\Traits;


use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFilesStub();
        UploadFilesStub::dropTable();
        UploadFilesStub::makeTable();
    }

    public function testMakeOldFieldsOnSaving()
    {
        $this->obj->fill([
            'name' => 'Tests',
            'file1' => 'video.mp4',
            'file2' => 'video2.mp4'
        ]);

        $this->obj->save();

        $this->assertCount(0, $this->obj->oldFiles);


        $this->obj->update([
            'name' => 'name_updated',
            'file2' => 'tests36.mp4'
        ]);

        $this->assertEqualsCanonicalizing(['video2.mp4'], $this->obj->oldFiles);

    }

    public function testMakeOldFilesNullOnSaving()
    {
        $this->obj->fill([
            'name' => 'Tests'
        ]);

        $this->obj->save();

        $this->obj->update([
            'name' => 'name_updated',
            'file2' => 'tests36.mp4'
        ]);

        $this->assertEqualsCanonicalizing([], $this->obj->oldFiles);

    }



}