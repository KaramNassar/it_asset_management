<?php

namespace Database\Seeders;

use App\AssetStatus;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branch = Branch::factory(3)->create();
        
        $department = Department::factory(5)->create();

        $employees = Employee::factory(10)->create([
            'branch_id' => fn() => $branch->random()->id,
            'department_id' => fn() => $department->random()->id,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'employee_id' => null,
        ]);

        $categories = Category::factory(5)->create();

        $assets = Asset::factory(20)->create([
            'category_id' => fn() => $categories->random()->id,
            'status' => AssetStatus::Available,
        ]);

        Loan::factory(8)->create([
            'employee_id' => fn() => $employees->random()->id,
            'asset_id' => function () use ($assets) {
                $asset = $assets->where('status', AssetStatus::Available)->random();
                $asset->update(['status' => AssetStatus::Assigned]);
                return $asset->id;
            },
            'is_active' => true,
        ]);

        Loan::factory(5)->returned()->create([
            'employee_id' => fn() => $employees->random()->id,
            'asset_id' => fn() => $assets->random()->id,
        ]);
    }
}
