<?php

namespace App\Filament\Pages\Reports;

use App\AssetCondition;
use App\Models\Category;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BranchInventoryReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Branch Inventory';

    protected static ?string $title = 'Branch Inventory';

    public ?int $branchId = null;

    protected string $view = 'filament.pages.reports.branch-inventory-report';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->sortable(),
            ])
            ->defaultSort('total', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $query = Category::query()
            ->join('assets', 'assets.category_id', '=', 'categories.id')
            ->join('loans', 'loans.asset_id', '=', 'assets.id')
            ->join('employees', 'employees.id', '=', 'loans.employee_id')
            ->join('branches', 'branches.id', '=', 'employees.branch_id')
            ->where('loans.is_active', true)
            ->where('loans.condition_on_delivery', AssetCondition::Excelent->value);

        return $query
            ->where('employees.branch_id', $this->branchId)
            ->selectRaw('categories.id, categories.name as category_name, branches.name as branch_name, COUNT(*) as total')
            ->groupBy('categories.id', 'categories.name', 'branches.name');
    }

    public function updatedBranchId(): void
    {
        $this->resetTable();
    }
}
