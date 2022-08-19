<?php

declare(strict_types=1);


namespace modules\edu\components;

use Faker\Factory;

class StudentLoginGenerator
{

    public static function generateLogin(): string
    {
        return sprintf("%02d", random_int(1, 99));
    }

    public static function generatePassword(): string
    {
        $faker = Factory::create();
        $color = $faker->colorName();
        return sprintf("%05d", random_int(1, 99999)) . $color;
    }
}
