<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'brand' => $this->faker->company,
            'category' => $this->faker->randomElement(['Electronics', 'Clothing', 'Food', 'Books']),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image_url' => $this->faker->imageUrl(),
            'digiflazz_id' => $this->faker->unique()->numerify('DF###'),
            'active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }
} 