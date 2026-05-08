<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentLoansTable extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->paginated([5])
            ->columns([
                Tables\Columns\TextColumn::make('loaned_at')
                    ->label('Loaned At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('asset.serial_number')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('asset.category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition_on_delivery')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->defaultSort('loaned_at', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return Loan::query()
            ->with(['employee', 'asset.category'])
            ->latest('loaned_at')
            ->limit(10);
    }
}
