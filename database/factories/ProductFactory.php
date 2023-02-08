<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use \App\Models\Category;
use \App\Models\Image;
use \App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->paragraph(),
            'enable' => fake()->boolean(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $product->images()->attach(
                Image::inRandomOrder()->where('enable', true)->take(random_int(3, 6))->pluck('id')
            );
            $product->categories()->attach(
                Category::inRandomOrder()->where('enable', true)->take(random_int(2, 4))->pluck('id')
            );
        });
    }
}
