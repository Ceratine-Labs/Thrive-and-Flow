<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'member',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    /** A member currently at the given pregnancy week. */
    public function pregnantWeek(int $week): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->addWeeks(40 - $week)->toDateString(),
        ]);
    }

    /** A member with a baby this many weeks old. */
    public function postpartumWeek(int $week): static
    {
        return $this->state(fn (array $attributes) => [
            'baby_born_at' => now()->subWeeks($week - 1)->subDays(2)->toDateString(),
        ]);
    }
}
