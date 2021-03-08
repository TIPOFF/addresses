<?php

declare(strict_types=1);

namespace Tipoff\Addresses\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Tipoff\Addresses\Models\Address;
use Tipoff\Addresses\Models\City;
use Tipoff\Addresses\Models\Timezone;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        $title = $this->faker->unique()->state;

        return [
            'title' => $title,
            'slug' => Str::slug($title),
        ];
    }
}