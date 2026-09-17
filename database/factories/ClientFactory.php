<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'transaction_type' => fake()->randomElement(['buy', 'rent']),
            'budget' => fake()->numberBetween(1_000_000, 10_000_000),
            'locations' => fake()->city(),
            'phone' => null,
            'contact_channel' => null,
            'bedrooms' => null,
            'minimum_size' => null,
            'transit_preference' => null,
            'notes' => null,
        ];
    }
}
