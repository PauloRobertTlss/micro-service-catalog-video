<?php

namespace Tests\Unit\Models\Traits;


use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;
use Tests\Traits\TestStorages;

class UploadFilesProdTest extends TestCase
{
    use TestStorages;
    /**
     * @var UploadFilesStub $obj */
    private $obj;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->obj = new UploadFilesStub();
        \Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
    }

    protected function tearDown(): void
    {
        $this->obj = null;
        parent::tearDown();

    }

    public function testUploadFile()
    {

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);

        \Storage::assertExists("1/{$file->hashName()}");



    }


    public function testUploadFiles()
    {
        $file02 = UploadedFile::fake()->create('video01.mp4');
        $file03 = UploadedFile::fake()->create('video02.mp4');

        $this->obj->uploadFiles([$file02, $file03]);

        \Storage::assertExists("1/{$file02->hashName()}");
        \Storage::assertExists("1/{$file03->hashName()}");

    }

    public function testDeleteFile()
    {

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);

        $fileName = $file->hashName();
        $this->obj->deleteFile($fileName);

        \Storage::assertMissing("1/{$fileName}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file);

        \Storage::assertMissing("1/{$file->hashName()}");

    }

    public function testDeleteOldFile()
    {

        $file = UploadedFile::fake()->create('video.mp4')->size(1);
        $file2 = UploadedFile::fake()->create('video2.mp4')->size(1);

        $this->obj->uploadFiles([$file, $file2]);
        $this->obj->deleteOldFiles();

        $this->assertCount(2, \Storage::allFiles());

        $this->obj->oldFiles = [$file2->hashName()];
        $this->obj->deleteOldFiles();


        \Storage::assertExists("1/{$file->hashName()}");
        \Storage::assertMissing("1/{$file2->hashName()}");

    }

    public function testDeleteFiles()
    {

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);

        $fileName = $file->hashName();
        $this->obj->deleteFile($fileName);

        \Storage::assertMissing("1/{$fileName}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file);

        \Storage::assertMissing("1/{$file->hashName()}");

    }


    public function testExtractFilesAttribute()
    {
        $attributes = [];

        $files = UploadFilesStub::extractFiles($attributes);

        $this->assertCount(0, $attributes);
        $this->assertCount(0, $files);


        $attributes = ['file_test' => true];
        $files = UploadFilesStub::extractFiles($attributes);

        $this->assertCount(1, $attributes);
        $this->assertCount(0, $files);

        $file = UploadedFile::fake()->create('video.mp4');
        $attributes = ['file_test' => $file, 'other' => 'tests'];
        $files = UploadFilesStub::extractFiles($attributes);

        $this->assertCount(2, $attributes);
        $this->assertEquals(['file_test' => $file->hashName(), 'other' => 'tests'], $attributes);
        $this->assertCount(1, $files);


    }






}
