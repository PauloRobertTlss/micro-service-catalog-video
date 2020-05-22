<?php

namespace Tests\Feature\Models\Video;


use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestStoreException;

class VideoUploadTest extends BaseVideoTestCase
{


    public function testCreateWithFiles()
    {
        \Storage::fake();

        $video = Video::create($this->data + [
                'thumb_file' => UploadedFile::fake()->create('thumb.jpg'),
                'video_file' => UploadedFile::fake()->create('video_file.mp4'),
            ]);

        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();

        \Event::listen(TransactionCommitted::class, function () {
            throw new TestStoreException();
        });

        $hasError = false;
        try {

            Video::create($this->data + [
                    'thumb_file' => UploadedFile::fake()->create('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->create('video_file.mp4'),
                ]);
        } catch (\Exception $exception) {

            $hasError = true;
            $this->assertCount(0, \Storage::allFiles());
        }

        $this->assertTrue($hasError);

    }


    public function testUpdateWithFiles()
    {
        \Storage::fake();

        $video = factory(Video::class)->create();
        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $videoFile = UploadedFile::fake()->create('video.mp4');

        $video->update($this->data + [
                'thumb_file' => $thumbFile,
                'video_file' => $videoFile,
            ]);

        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");

        $newVideoFile = UploadedFile::fake()->create('update.mp3');

        $video->update($this->data + ['video_file' => $newVideoFile]);

        \Storage::assertExists("{$video->id}/{$thumbFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        \Storage::assertMissing("{$video->id}/{$videoFile->hashName()}");

    }

    public function testUpdateIfRollbackFiles()
    {
        \Storage::fake();

        $video = factory(Video::class)->create();
        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $videoFile = UploadedFile::fake()->create('video.mp4');

        \Event::listen(TransactionCommitted::class, function (){
            throw new TestStoreException();
        });


        $hasError = false;
        try {

            $video->update($this->data + [
                    'thumb_file' => $thumbFile,
                    'video_file' => $videoFile,
                ]);

        } catch (\Exception $exception) {

            $hasError = true;
            $this->assertCount(0, \Storage::allFiles());
        }

        $this->assertTrue($hasError);

    }

    public function testFileUrlWithLocalDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field)
        {
            $fileFields[$field] = "{$field}.test";
        }

        $video = factory(Video::class)->create($fileFields);
        $localDriver = config('filesystems.default');
        $baseUrl = config('filesystems.disks.' . $localDriver)['url'];

        foreach ($fileFields as $field => $value)
        {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/{$video->id}/{$value}", $fileUrl);
        }
    }

    public function testFileUrlWithGcsDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field)
        {
            $fileFields[$field] = "{$field}.test";
        }

        $video = factory(Video::class)->create($fileFields);
        $baseUrl = config('filesystems.disks.gcs.storage_api_uri');
        \Config::set('filesystems.default', 'gcs');

        foreach ($fileFields as $field => $value)
        {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/{$video->id}/{$value}", $fileUrl);
        }
    }

    public function testFileUrlIfNullWhenFieldAreNull()
    {

        $video = factory(Video::class)->create();
        foreach (Video::$fileFields as $field)
        {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertNull($fileUrl);
        }
    }
}
