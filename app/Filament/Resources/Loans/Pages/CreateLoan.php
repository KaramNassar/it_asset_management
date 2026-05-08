<?php

namespace App\Filament\Resources\Loans\Pages;

use App\AssetStatus;
use App\Filament\Resources\Loans\LoanResource;
use App\Models\Asset;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = empty($data['returned_at']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $loan = $this->record;

        if ($loan?->is_active) {
            Asset::query()
                ->whereKey($loan->asset_id)
                ->update(['status' => AssetStatus::Assigned]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
