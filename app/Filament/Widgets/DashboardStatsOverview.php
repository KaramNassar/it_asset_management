<?php

namespace App\Filament\Widgets;

use App\AssetStatus;
use App\Models\Asset;
use App\Models\Loan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAssets = Asset::query()->count();
        $availableAssets = Asset::query()->where('status', AssetStatus::Available)->count();
        $assignedAssets = Asset::query()->where('status', AssetStatus::Assigned)->count();
        $maintenanceAssets = Asset::query()->where('status', AssetStatus::Maintenance)->count();
        $brokenAssets = Asset::query()->where('status', AssetStatus::Broken)->count();
        $activeLoans = Loan::query()->where('is_active', true)->count();
        $pendingInspections = Loan::query()
            ->whereNotNull('returned_at')
            ->whereNull('condition_on_return')
            ->count();

        return [
            Stat::make('Total Assets', $totalAssets)
                ->description('All registered assets in the system')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('info'),

            Stat::make('Available', $availableAssets)
                ->description('Ready to be loaned')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Assigned', $assignedAssets)
                ->description('Currently with employees')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('primary'),

            Stat::make('Active Loans', $activeLoans)
                ->description('Ongoing loan records')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Pending Inspections', $pendingInspections)
                ->description('Returned assets awaiting inspection')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('In Maintenance', $maintenanceAssets)
                ->description('Undergoing inspection or maintenance')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Broken', $brokenAssets)
                ->description('Out of service')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
