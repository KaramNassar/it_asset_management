<?php

namespace App\Filament\Pages\Reports;

use App\Models\Employee;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HeavyUsageReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Heavy Usage';

    protected static ?string $title = 'Heavy Usage';

    protected string $view = 'filament.pages.reports.heavy-usage-report';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch'),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department'),

                Tables\Columns\TextColumn::make('assets_count')
                    ->label('Distinct assets (last 6 months)')
                    ->sortable(),
            ])
            ->defaultSort('assets_count', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $since = now()->subMonths(6);

        return Employee::query()
            ->with(['branch', 'department'])
            ->join('loans', 'loans.employee_id', '=', 'employees.id')
            ->where('loans.loaned_at', '>=', $since)
            ->selectRaw(
                'employees.id,
                employees.number,
                employees.name,
                employees.department_id,
                employees.branch_id,
                COUNT(DISTINCT loans.asset_id) as assets_count')
            ->groupBy(
                'employees.id',
                'employees.number',
                'employees.name',
                'employees.department_id',
                'employees.branch_id'
            )
            ->having('assets_count', '>', 3);
    }
}
