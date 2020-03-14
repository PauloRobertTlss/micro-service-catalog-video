<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCategories()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidateData()
    {
        $rules = ['name','is_active'];

        $response = $this->json('POST',route('categories.store'), []);

        $this->assertInvalidateData($response);

        $response = $this->json('POST',route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => "a"
        ]);

        $this->assertInvalidateMaxAndBoolean($response);

        $category = factory(Category::class)->create();
        $response = $this->json('PUT',route('categories.update', ['category' => $category->id]), []);

        $this->assertInvalidateData($response);

        $response = $this->json('PUT',route('categories.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => "a"
        ]);

        $this->assertInvalidateMaxAndBoolean($response);

    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store', [
            'name' => 'tests',
            'is_active' => true
        ]));

        $category = Category::find($response->json('id'));

        $response->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($category->is_active);
        $this->assertNull($category->description);

        $desc = str_repeat('abf', 100);
        $response = $this->json('POST', route('categories.store', [
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
        $category = factory(Category::class)->create([
            'name' => 'Testes',
            'is_active' => true,
            'description' => 'Testes Description'
        ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'Tests Update',
            'is_active' => false,
            'description' => null
        ]);

        $category = Category::find($response->json('id'));

        $response->assertStatus(200)
            ->assertJson($category->toArray());

        $this->assertFalse($category->is_active);
        $this->assertNull($category->description);
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
