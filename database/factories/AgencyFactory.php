<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AgencyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company() . ' Realty';
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'logo' => null,
        ];
    }
}