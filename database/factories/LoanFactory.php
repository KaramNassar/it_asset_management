<?php

namespace Database\Factories;

use App\AssetCondition;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'asset_id' => Asset::factory(),
            'loaned_at' => now()->subMonths(rand(1, 6)), 
            'condition_on_delivery' => AssetCondition::Excelent,
            'returned_at' => null,
            'condition_on_return' => null,
            'is_active' => true,
        ];
    }

    public function returned(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
            'returned_at' => now()->subDays(rand(1, 15)),
            'condition_on_return' => AssetCondition::Good,
        ]);
    }

    public function returnedDamaged(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
            'returned_at' => now()->subDays(2),
            'condition_on_return' => AssetCondition::Damaged,
        ]);
    }

}
