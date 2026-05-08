<?php

namespace App\Filament\Resources\Assets\Pages;

use App\AssetStatus;
use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageAssets extends ManageRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'available' => Tab::make('Available')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Available)),

            'assigned' => Tab::make('Assigned')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Assigned)),

            'maintenance' => Tab::make('Maintenance')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Maintenance)),

            'broken' => Tab::make('Broken')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Broken)),

            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Archived)),
        ];
    }
}
