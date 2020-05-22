<?php

namespace Tests\Feature\Http\Controllers\Api\Video;

use App\Http\Controllers\Api\VideoController;
use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Arr;
use Tests\Exceptions\TestStoreException;
use Tests\Traits\TestResources;
use Tests\Traits\TestStore;
use Tests\Traits\TestValidations;
use Illuminate\Http\Request;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{

    use TestValidations, TestStore, TestResources;

    private $fieldsSerialized = [
        'id',
        'title',
        'description',
        'year_launched',
        'rating',
        'opened',
        'duration',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ],
        'genres' => [
            '*' => [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ],
        'thumb_file_url',
        'banner_file_url',
        'trailer_file_url',
        'video_file_url'
    ];

    public function testVideos()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->fieldsSerialized
                ],
                'meta' => [],
                'links' => [],
            ]);

        $this->assertResource($response, VideoResource::collection(collect([$this->video])));
        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }


    public function testInvalidationRequired()
    {
        $fields = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];

        $this->assertInvalidationStoreAction($fields, 'required');
        $this->assertInvalidationUpdateAction($fields, 'required');
    }

    public function testInvalidateMax()
    {
        $data = [
            'title' => str_repeat('a', 256),
        ];

        $this->assertInvalidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateAction($data, 'max.string', ['max' => 255]);

    }

    public function testInvalidateBoolean()
    {

        $data = [
            'opened' => "a"
        ];

        $this->assertInvalidationStoreAction($data, 'boolean');
        $this->assertInvalidationUpdateAction($data, 'boolean');

    }

    public function testInvalidateCategoriesIdField()
    {

        $data = [
            'categories_id' => "a"
        ];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = [
            'categories_id' => [154222]
        ];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function testInvalidateGenresIdField()
    {

        $data = [
            'genres_id' => "a"
        ];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = [
            'genres_id' => [154222]
        ];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function testInvalidateInteger()
    {

        $data = [
            'duration' => "a"
        ];

        $this->assertInvalidationStoreAction($data, 'integer');
        $this->assertInvalidationUpdateAction($data, 'integer');

    }

    public function testInvalidationYearLaunchedField()
    {

        $data = [
            'year_launched' => "a"
        ];

        $this->assertInvalidationStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationUpdateAction($data, 'date_format', ['format' => 'Y']);

    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0
        ];

        $this->assertInvalidationStoreAction($data, 'in');
        $this->assertInvalidationUpdateAction($data, 'in');

    }

    public function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoryId,
            'video_id' => $videoId,
        ]);
    }

    public function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genreId,
            'video_id' => $videoId,
        ]);
    }


    public function testDestroy()
    {

        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function assertInvalidateData(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'required', []);
    }

    protected function assertInvalidateMaxAndBoolean(TestResponse $response)
    {

        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
        $this->assertInvalidationFields($response, ['is_active'], 'validation.boolean', []);
    }


    public function testSave()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync([$category->id]);

        $data = [
            ['send_data' => $this->sendData + [
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + ['opened' => false]
            ],
            ['send_data' => $this->sendData + [
                    'opened' => true,
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + ['rating' => Video::RATING_LIST[1]]
            ]
        ];


        foreach ($data as $key => $test) {
            $response = $this->assertStore($test['send_data'], $test['test_data'] + ['deleted_at' => null]);

            $response->assertJsonStructure([
                'data'=> $this->fieldsSerialized
            ]);

            $this->assertResource($response,
                new VideoResource(Video::find($response->json('data.id'))));

            $this->assertHasCategory($response->json('data.id'), $test['send_data']['categories_id'][0]);
            $this->assertHasGenre($response->json('data.id'), $test['send_data']['genres_id'][0]);

            $response = $this->assertUpdate($test['send_data'], $test['test_data'] + ['deleted_at' => null]);

            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

            $this->assertResource($response,
                new VideoResource(Video::find($response->json('data.id'))));
        }
    }

    public function testSaveWithoutFiles()
    {

        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);

        $data = [
            ['send_data' => $this->sendData,
                'test_data' => $testData + ['opened' => false]
            ],
            ['send_data' => $this->sendData + [
                    'opened' => true
                ],
                'test_data' => $testData + ['rating' => Video::RATING_LIST[1]]
            ]
        ];


        foreach ($data as $key => $test) {
            $response = $this->assertStore($test['send_data'], $test['test_data'] + ['deleted_at' => null]);

            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

            $this->assertHasCategory($response->json('data.id'), $test['send_data']['categories_id'][0]);
            $this->assertHasGenre($response->json('data.id'), $test['send_data']['genres_id'][0]);

            $response = $this->assertUpdate($test['send_data'], $test['test_data'] + ['deleted_at' => null]);

            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);
        }
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoriesId);
        $genreId = $genre->id;

        $response = $this->json('POST', $this->routeStore(), $this->sendData + [
                'genres_id' => [$genreId],
                'categories_id' => [$categoriesId[0]],
            ]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $response->json('data.id')
        ]);

        $response = $this->json('PUT', route('videos.update', ['video' => $response->json('data.id')]),
            $this->sendData + [
                'genres_id' => [$genreId],
                'categories_id' => [$categoriesId[1], $categoriesId[2]]
            ]);


        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $response->json('data.id')
        ]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $response->json('data.id')
        ]);

    }


    public function testSyncGenre()
    {
        $genres = factory(Genre::class, 3)->create();
        $genresId = $genres->pluck('id')->toArray();

        $categoriesId = factory(Category::class)->create()->id;
        $genres->each(function ($genre) use ($categoriesId) {
            $genre->categories()->sync($categoriesId);
        });


        $response = $this->json('POST', $this->routeStore(), $this->sendData + [
                'categories_id' => [$categoriesId],
                'genres_id' => [$genresId[0]]
            ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $response->json('data.id')
        ]);

        $response = $this->json('PUT', route('videos.update', ['video' => $response->json('data.id')]),
            $this->sendData + [
                'categories_id' => [$categoriesId],
                'genres_id' => [$genresId[1], $genresId[2]],
            ]);


        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $response->json('data.id')
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $response->json('data.id')
        ]);

    }

    public function testRollBackStore()
    {

        //simula todos dos metodos
        $controller = \Mockery::mock(VideoController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestStoreException('tests'));

        $controller->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('get')
            ->withAnyArgs()
            ->andReturnNull();

        try {

            $controller->store($request);

        } catch (TestStoreException $exception) {

            $this->assertCount(1, Video::all());
        }


    }


    public function testRollBackUpdate()
    {

        //simula todos dos metodos
        $controller = \Mockery::mock(VideoController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestStoreException('tests'));

        $controller->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->video);

        $controller->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('get')
            ->withAnyArgs()
            ->andReturnNull();

        $hasError = false;

        try {

            $controller->update($request, 1);

        } catch (TestStoreException $exception) {

            $this->assertCount(1, Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);


    }

}
