<?php

namespace Database\Factories;

use App\AssetStatus;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serial_number' => strtoupper($this->faker->bothify('SN-####-####')),
            'model' => $this->faker->word,
            'category_id' => Category::factory(),
            'status' => AssetStatus::Available,
            'purchase_date' => $this->faker->date(),
        ];
    }
}
