<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'description' => $faker->text,
        'completed_at' => function () {
            if (rand(0, 1)) {
                return Carbon::now()->format('Y-m-d H:m:s');
            }

            return null;
        }
    ];
});
