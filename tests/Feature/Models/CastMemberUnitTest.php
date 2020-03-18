<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CastMemberUnitTest extends TestCase
{
    use DatabaseMigrations;

    private $category;
    
    
    public function testList()
    {
        factory(CastMember::class, 1)->create();

        $collection = CastMember::all();
        $attributeKeys = array_keys($collection->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'type',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $attributeKeys);
    }

    public function testCreate()
    {
        $category = CastMember::create([
            'name' => 'First',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('First', $category->name);

        $category = CastMember::create([
            'name' => 'First',
            'type' => CastMember::TYPE_ACTOR
        ]);
        $category->refresh();
        $this->assertEquals($category->type, CastMember::TYPE_ACTOR);

    }

    public function testUpdate()
    {
        $category = factory(CastMember::class)->create([
            'name' => 'First Actor',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $data = [
            'name' => 'First Director',
            'type' => CastMember::TYPE_DIRECTOR
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(CastMember::class)->create();
        $category->delete();

        $this->assertNull(CastMember::find($category->id));
    }
}
