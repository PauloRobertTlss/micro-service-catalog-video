<?php

namespace Tests\Feature\Models\Video;


use App\Models\Video;

class VideoCrudTest extends BaseVideoTestCase
{
    public function testList()
    {
        factory(Video::class)->create();
        $videos = Video::all();

        $this->assertCount(1, $videos);
        $videosKeys = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $videos);
    }

    public function testCreateWithBasicFields()
    {

        $video = Video::create($this->data + $this->fileFields);
        $video->refresh();

        $this->assertEquals(36,strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + $this->fileFields);

        $this->data['opened'] = true;
        $video = Video::create($this->data + $this->fileFields);

        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + $this->fileFields + ['opened' => true]);

    }


    public function testCreateWithRelations()
    {
        $genre = factory(Genre::class)->create();
        $category = factory(Category::class)->create();
        $video = Video::create($this->data + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
            ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);

    }

    protected function assertHasCategory(string $id, string $relationId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $id,
            'category_id' => $relationId
        ]);
    }

    protected function assertHasGenre(string $id, string $relationId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $id,
            'genre_id' => $relationId
        ]);
    }


    public function testUpdateWithBaseFields()
    {
        $video = factory(Video::class)->create([
            'opened' => false
        ]);

        $video->update($this->data);
        $this->assertFalse($video->opened);

        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = factory(Video::class)->create([
            'opened' => false
        ]);

        $video->update($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    public function testUpdateWithRelations()
    {
        $genre = factory(Genre::class)->create();
        $category = factory(Category::class)->create();
        $video = factory(Video::class)->create();

        $video = $video->update($this->data + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
            ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);

    }

    public function testHandleRelations()
    {
        $genre = factory(Genre::class)->create();
        $category = factory(Category::class)->create();
        $video = factory(Video::class)->create();

        Video::handleRelations($video, []);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        Video::handleRelations($video, [
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);



    }



    public function testRollBackCreate()
    {
        $hasError = false;

        try {
            $obj = Video::create([
                'title' => 'Test Video Test',
                'description' => 'Testes phpunit',
                'year_launched' => 2019,
                'opened' => false,
                'rating' => Video::RATING_LIST[0],
                'duration' => rand(30, 60),
                'categories_id' => [0,1,2]
            ]);

        } catch (QueryException $exception) {
            $hasError = true;
            $this->assertCount(0, Video::all());
        }

        $this->assertTrue($hasError);


    }


    public function testRollBackUpdate()
    {
        $hasError = false;

        $video = factory(Video::class)->create();
        $oldTitle = $video->title;

        try {

            $obj = $video->update([
                'title' => 'Test Video Test',
                'description' => 'Testes phpunit',
                'year_launched' => 2019,
                'opened' => false,
                'rating' => Video::RATING_LIST[0],
                'duration' => rand(30, 60),
                'categories_id' => [0,1,2]
            ]);

        } catch (QueryException $exception) {
            $hasError = true;
            $this->assertDatabaseHas('videos', ['title' => $oldTitle]);
            $this->assertCount(0, Video::all());
        }

        $this->assertTrue($hasError);


    }

}