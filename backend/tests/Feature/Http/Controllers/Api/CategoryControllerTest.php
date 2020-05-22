<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestResources;
use Tests\Traits\TestStore;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations, TestStore, TestResources;

    private $category;

    private $serializedFields = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                   '*' => $this->serializedFields //'*' . qualquer index  - dinamico
                ],
                'links' => [],
                'meta' => [],
            ]);

        $resource = CategoryResource::collection(collect([$this->category]));
        $this->assertResource($response, $resource);


    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $this->assertResource($response, $resource);


    }

    public function testInvalidateData()
    {
        $data = ['name' => ''];

        $this->assertInvalidationStoreAction($data, 'required');
        $this->assertInvalidationUpdateAction($data, 'required');


        $data = [
            'name' => str_repeat('a', 256),
        ];

        $this->assertInvalidationStoreAction($data, 'max.string',['max' => 255]);
        $this->assertInvalidationUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => "a"
        ];

        $this->assertInvalidationStoreAction($data, 'boolean');
        $this->assertInvalidationUpdateAction($data, 'boolean');

    }

    public function testStore()
    {
        $data = [
            'name' => 'tests',
            'is_active' => true
        ];

        $response = $this->assertStore($data, $data + ['description' => null]);

        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $data = [
            'name' => 'tests',
            'description' => 'description',
            'is_active' => false
        ];

        $response = $this->assertStore($data, $data + ['description' => 'description']);
        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $this->assertResource($response, $resource);

    }


    public function testUpdate()
    {
        $this->category = factory(Category::class)->create([
            'name' => 'Testes',
            'is_active' => true,
            'description' => 'Testes Description'
        ]);

        $data = [
            'name' => 'Tests Update',
            'is_active' => false,
            'description' => null
        ];

        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $this->assertResource($response, $resource);

        $data['description'] = 'Test';

        $this->assertUpdate($data, $data + ['description' => 'test']);
    }


    public function testDestroy()
    {

        $response = $this->json('DELETE', route('categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    protected function assertInvalidateData(TestResponse $response)
    {
        $this->assertInvalidationFields($response,['name'], 'required', []);
    }

    protected function assertInvalidateMaxAndBoolean(TestResponse $response)
    {

        $this->assertInvalidationFields($response,['name'], 'max.string', ['max' => 255]);
        $this->assertInvalidationFields($response,['is_active'], 'validation.boolean', []);
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }

}