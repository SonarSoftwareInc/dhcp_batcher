<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\DhcpServer::class, function (Faker $faker) {
    static $password;

    return [
        'name' => str_random(16),
        'username' => str_random(16),
        'password' => $password ?: $password = bcrypt('secret'),
    ];
});
