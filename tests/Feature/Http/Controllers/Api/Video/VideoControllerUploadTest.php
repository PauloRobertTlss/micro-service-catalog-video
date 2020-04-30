<?php

namespace Tests\Feature\Http\Controllers\Api\Video;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestStoreException;
use Illuminate\Http\Request;
use Tests\Traits\TestUploads;

class VideoControllerUploadTest extends BaseVideoControllerTestCase
{

    use TestUploads;

    public function testInvalidationVideoField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            12,
            'mimetypes', ['values' => 'videos/mp4']
        );
    }

    public function testSaveWithoutFiles()
    {


    }

    public function testStoreWithFiles()
    {
        \Storage::fake();

        $files = $this->getFiles();
        $genre = factory(Genre::class)->create();
        $category = factory(Category::class)->create();
        $genre->categories()->sync($category);

        $response = $this->json('POST', $this->routeStore(), $this->sendData + [
                'genres_id' => [$genre->id],
                'categories_id' => [$category->id],
            ] + $files);

        $response->assertStatus(201);
        $id = $response->json('id');

        foreach ($files  as $file)
        {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();

        $files = $this->getFiles();

        $genres = factory(Genre::class, 3)->create();
        $genresId = $genres->pluck('id')->toArray();
        $categoriesId = factory(Category::class)->create()->id;

        $genres->each(function ($genre) use ($categoriesId) {
            $genre->categories()->sync($categoriesId);
        });

        $response = $this->json('PUT', $this->routeUpdate(), $this->sendData + [
                'genres_id' => $genresId,
                'categories_id' => [$categoriesId],
            ] + $files);

        $response->assertStatus(200);
        $id = $this->video->id;

        foreach ($files  as $file)
        {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create('video_file.mp4')
        ];
    }


}
