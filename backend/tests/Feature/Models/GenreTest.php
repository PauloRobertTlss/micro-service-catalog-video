<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreUnitTest extends TestCase
{
    use DatabaseMigrations;

    private $category;
    
    
    public function testList()
    {
        factory(Genre::class)->create();

        $collection = Genre::all();
        $attributeKeys = array_keys($collection->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $attributeKeys);
    }

    public function testCreate()
    {
        $category = Genre::create([
            'name' => 'First'
        ]);

        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('First', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue((bool)$category->is_active);

        $category = Genre::create([
            'name' => 'First',
            'description' => null
        ]);
        $category->refresh();
        $this->assertNull($category->description);

        $category = Genre::create([
            'name' => 'First',
            'is_active' => false
        ]);
        $category->refresh();
        $this->assertFalse($category->is_active);

    }

    public function testUpdate()
    {
        $category = factory(Genre::class)->create([
            'description' => 'First_description',
            'is_active' => true
        ]);

        $data = [
            'name' => 'Name Update',
            'description' => 'testes_description_update',
            'is_active' => false
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Genre::class)->create();
        $category->delete();

        $this->assertNull(Genre::find($category->id));
    }
}
