<?php

namespace Database\Factories;

use App\Models\LobbyCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LobbyCode>
 */
class LobbyCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('[A-Za-z0-9]{12}'),
            'reward' => fake()->randomElement([
                '2,000 Sprite Dust',
                '5,000 Sprite Dust',
                '40,000 XP',
                '2x Cheat Code Locator',
                '2x Extraction Accelerator',
            ]),
            'requirement' => null,
            'is_expired' => false,
            'redeemed_at' => null,
        ];
    }

    /**
     * Indicate that the code has been redeemed.
     */
    public function redeemed(): static
    {
        return $this->state(fn (array $attributes) => [
            'redeemed_at' => now(),
        ]);
    }

    /**
     * Indicate that the code has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_expired' => true,
        ]);
    }
}
