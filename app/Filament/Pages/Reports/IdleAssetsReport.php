<?php

namespace App\Filament\Pages\Reports;

use App\Models\Asset;
use App\Models\Loan;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class IdleAssetsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Idle Assets';

    protected static ?string $title = 'Idle Assets';

    protected string $view = 'filament.pages.reports.idle-assets-report';

    public int $days = 30;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_loaned_at')
                    ->label('Last loaned at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_in_stock')
                    ->label('Days in stock')
                    ->state(function ($record) {
                        $baseDate = $record->last_loaned_at ?? $record->purchase_date ?? $record->created_at;

                        return abs(round(now()->diffInDays($baseDate)));

                    })
                    ->sortable(),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $lastLoanSub = Loan::query()
            ->selectRaw('asset_id, MAX(loaned_at) as last_loaned_at')
            ->groupBy('asset_id');

        $cutoff = now()->subYear();

        return Asset::query()
            ->with('category')
            ->leftJoinSub($lastLoanSub, 'last_loans', function ($join) {
                $join->on('assets.id', '=', 'last_loans.asset_id');
            })
            ->whereNotIn('assets.id', function ($q) {
                $q->select('asset_id')
                    ->from('loans')
                    ->where('is_active', true);
            })
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_loans.last_loaned_at')
                    ->orWhere('last_loans.last_loaned_at', '<', $cutoff);
            })
            ->select([
                'assets.*',
                'last_loans.last_loaned_at',
            ]);
    }
}
