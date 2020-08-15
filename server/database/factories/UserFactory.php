<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        // password is word password
        'password' => '$2y$10$1ZbyLUnMkiSmkGZMuBwLSupg1mvQrg4LZmUrqei5I2coRGYOfQN3G',
        'email' => $faker->unique->safeEmail(),
        'email_verified_at' => Carbon::now()->format('Y-m-d H:m:s')
    ];
});
