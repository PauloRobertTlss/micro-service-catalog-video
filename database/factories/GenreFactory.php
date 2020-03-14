<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Genre::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'description' => rand(1,10) % 2 == 0 ? $faker->sentence() :null
    ];
});
