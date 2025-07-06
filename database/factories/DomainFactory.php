<?php

namespace Database\Factories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    protected $model = Domain::class;

    public function definition(): array
    {
        return [
            'host' => $this->faker->unique()->domainName().':'.$this->faker->numberBetween(1000, 9999),
            'protocol' => $this->faker->randomElement(['http', 'https']),
            'created_at' => now(),
        ];
    }
}
