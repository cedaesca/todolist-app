<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TasksList;
use Faker\Generator as Faker;

$factory->define(TasksList::class, function (Faker $faker) {
    return [
        'name' => $faker->text
    ];
});
