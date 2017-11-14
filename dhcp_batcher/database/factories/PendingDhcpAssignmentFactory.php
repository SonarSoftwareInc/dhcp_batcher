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

$factory->define(App\PendingDhcpAssignment::class, function (Faker $faker) {
    return [
        'leased_mac_address' => $faker->macAddress,
        'ip_address' => $faker->localIpv4,
        'expired' => false,
    ];
});
