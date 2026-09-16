<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'transaction_type' => 'sale',
            'name' => fake()->streetName().' Residence',
            'price' => fake()->numberBetween(1_000_000, 10_000_000),
            'bedrooms' => fake()->numberBetween(0, 3),
            'location' => fake()->city(),
            'status' => Property::DEFAULT_STATUS,
        ];
    }
}
