<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Field>
 */
class FieldFactory extends Factory
{
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Lapangan ' . fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'type' => fake()->randomElement(['indoor', 'outdoor']),
            'description' => fake()->paragraph(),
            'facilities' => ['Lantai Standar', 'Penerangan LED', 'Ruang Ganti'],
            'image_path' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the field is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
