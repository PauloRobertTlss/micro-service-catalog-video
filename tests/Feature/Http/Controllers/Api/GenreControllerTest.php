<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GenreController;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\Exceptions\TestStoreException;
use Tests\TestCase;
use Tests\Traits\TestStore;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations, TestStore;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->genre = factory(Genre::class)->create();
        $this->sendData = [
            'name' => 'Adulto',
            'is_active' => false
        ];
    }

    public function testCategories()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidateData()
    {
        $rules = ['name','is_active'];

        $response = $this->json('POST',route('genres.store'), []);

        $this->assertInvalidateData($response);

        $response = $this->json('POST',route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => "a"
        ]);

        $this->assertInvalidateMaxAndBoolean($response);

        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT',route('genres.update', ['genre' => $genre->id]), []);

        $this->assertInvalidateData($response);

        $response = $this->json('PUT',route('genres.update', ['genre' => $genre->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => "a"
        ]);

        $this->assertInvalidateMaxAndBoolean($response);

    }

    public function testInvalidationRequired()
    {
        $fields = [
            'name' => '',
            'categories_id' => ''
        ];

        $this->assertInvalidationStoreAction($fields, 'required');
        $this->assertInvalidationUpdateAction($fields, 'required');

        $fields = [
            'categories_id' => 'a'
        ];

        $this->assertInvalidationStoreAction($fields, 'array');
        $this->assertInvalidationUpdateAction($fields, 'array');

        $fields = [
            'categories_id' => [100]
        ];

        $this->assertInvalidationStoreAction($fields, 'exists');
        $this->assertInvalidationUpdateAction($fields, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $fields = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationStoreAction($fields, 'exists');
        $this->assertInvalidationUpdateAction($fields, 'exists');


    }

    public function testStore()
    {

        $categoryId = factory(Category::class)->create()->id;

        $data = [
            'name' => 'tests',
            'is_active' => true
        ];

        $response = $this->assertStore($data + ['categories_id' => [$categoryId]],
            $data + ['deleted_at' => null]);

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $this->assertHasCategory($response->json('id'), $categoryId);

        $data = [
            'name' => 'tests',
            'is_active' => false
        ];

        $response = $this->assertStore($data + ['categories_id' => [$categoryId]],
            $data + ['deleted_at' => null]);


    }


    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'Testes',
            'is_active' => true,
            'description' => 'Testes Description'
        ]);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'Tests Update',
            'is_active' => false,
            'description' => null
        ]);

        $genre = Genre::find($response->json('id'));

        $response->assertStatus(200)
            ->assertJson($genre->toArray());

        $this->assertFalse($genre->is_active);
        $this->assertNull($genre->description);
    }


    public function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genre', [
           'genre_id' => $genreId,
           'category_id' => $categoryId
        ]);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();


        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->json('POST', $this->routeStore(), $sendData);

        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id'=> $response->json('id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];

        $response = $this->json('PUT', route('genres.update', ['genre' => $response->json('id')]), $sendData);


        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id'=> $response->json('id')
        ]);

        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id'=> $response->json('id')
        ]);

    }



    public function testDestroy()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'Testes',
            'is_active' => true,
            'description' => 'Testes Description'
        ]);

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }

    protected function assertInvalidateData(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.required',['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidateMaxAndBoolean(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string',['attribute' => 'name', 'max' => 255])
            ])->assertJsonFragment([
                \Lang::get('validation.boolean',['attribute' => 'is active'])
            ]);
    }


    public function testRollBackStore()
    {

        //simula todos dos metodos
        $controller = \Mockery::mock(GenreController::class)
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

        $hasError = false;

        try {

            $controller->store($request);

        } catch (TestStoreException $exception) {

            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }


    public function testRollBackUpdate()
    {

        //simula todos dos metodos
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestStoreException('tests'));

        $controller->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);

        $controller->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $request = \Mockery::mock(Request::class);

        $hasError = false;

        try {

            $controller->update($request, 1);

        } catch (TestStoreException $exception) {

            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);


    }

    protected function model()
    {
        return Genre::class;
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }
}
