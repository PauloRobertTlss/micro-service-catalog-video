<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

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

    public function testStore()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => 'tests',
            'is_active' => true
        ]));

        $genre = Genre::find($response->json('id'));

        $response->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($genre->is_active);
        $this->assertNull($genre->description);

        $desc = str_repeat('abf', 100);
        $response = $this->json('POST', route('genres.store', [
            'name' => 'tests',
            'description' => $desc,
            'is_active' => false
        ]));

        $response->assertJsonFragment([
            'description' => $desc,
            'is_active' => false
        ]);
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


    public function testDestroy()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'Testes',
            'is_active' => true,
            'description' => 'Testes Description'
        ]);

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));
        $response->assertStatus(422);
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

}
