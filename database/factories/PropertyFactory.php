<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Bali', 'Medan'];
        $types = ['House', 'Apartment', 'Villa', 'Ruko', 'Land'];
        $districts = ['Menteng', 'Dago', 'Tegalsari', 'Canggu', 'Polonia'];
        
        $selectedCity = fake()->randomElement($cities);
        $type = fake()->randomElement($types);
        $title = $type . ' ' . fake()->words(3, true) . ' in ' . $selectedCity;

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraph(3),
            'price' => fake()->numberBetween(500, 20000) * 1000000, // 500jt - 20M
            'listing_type' => fake()->randomElement(['SALE', 'RENT']),
            'category' => fake()->randomElement(['RESIDENTIAL', 'COMMERCIAL']),
            'property_type' => $type,
            'city' => $selectedCity,
            'district' => fake()->randomElement($districts),
            'address' => fake()->streetAddress(),
            
            // Random Coordinates (Centered roughly around Indonesia)
            'latitude' => fake()->latitude(-8.0, -6.0), 
            'longitude' => fake()->longitude(106.0, 115.0),
            
            'bedrooms' => fake()->numberBetween(1, 6),
            'bathrooms' => fake()->numberBetween(1, 5),
            'land_area' => fake()->numberBetween(60, 500),
            'building_area' => fake()->numberBetween(45, 400),
            'specifications' => [
                'electricity' => fake()->randomElement(['1300', '2200', '4400', '6600']) . ' Watt',
                'certificate' => fake()->randomElement(['SHM', 'HGB']),
                'floors' => fake()->numberBetween(1, 3)
            ],
            'status' => 'PUBLISHED',
            'views' => fake()->numberBetween(0, 1000),
            
            // We assign a user later in the Seeder
            'user_id' => User::factory(), 
        ];
    }
}