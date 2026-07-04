<?php

declare(strict_types=1);

use Vaened\Authorization\Models\Permission;
use Vaened\Authorization\Models\Role;
use Vaened\Authorization\Tests\Support\Models\User;
use Faker\Generator;
use Illuminate\Support\Str;

$factory->define(Permission::class, function (Generator $faker): array {
    $name = $faker->unique()->name;

    return [
        'secret_name' => Str::slug($name, '.'),
        'display_name' => $name,
    ];
});

$factory->define(Role::class, function (Generator $faker): array {
    $name = $faker->unique()->name;

    return [
        'secret_name' => Str::slug($name, '.'),
        'display_name' => $name,
    ];
});

$factory->define(User::class, function (Generator $faker): array {
    return [
        'email' => $faker->email,
    ];
});
