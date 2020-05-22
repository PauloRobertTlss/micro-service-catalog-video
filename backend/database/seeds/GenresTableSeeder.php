<?php

use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = \App\Models\Category::all();
        factory(\App\Models\Genre::class, 100)
            ->create()
            ->each(function ($genre) use ($categories) {
                $categoriesId = $categories->random(6)->pluck('id')->toArray();
                $genre->categories()->attach($categoriesId);
            });
    }
}
