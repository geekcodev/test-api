<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;


class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    public function withVerifiedEmail(string $email): UserFactory
    {
        return $this->state(fn() => [
            'email' => $email,
            'email_verified_at' => Carbon::now(),
        ]);
    }

    public function withPassword(string $password): UserFactory
    {
        return $this->state(fn() => ['password' => Hash::make($password)]);
    }

    public function withoutRememberToken(): UserFactory
    {
        return $this->state(fn() => ['remember_token' => null]);
    }
}
