<?php

namespace App\Filament\Resources\Loans\Pages;

use App\AssetStatus;
use App\Filament\Resources\Loans\LoanResource;
use App\Models\Asset;
use App\Models\Loan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = empty($data['returned_at']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $asset = Asset::query()
                ->whereKey($data['asset_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($asset->status !== AssetStatus::Available) {
                throw ValidationException::withMessages([
                    'asset_id' => 'This asset is not available for loan.',
                ]);
            }

            $hasActiveSameCategory = Loan::query()
                ->where('employee_id', $data['employee_id'])
                ->where('is_active', true)
                ->whereHas('asset', function ($query) use ($asset) {
                    $query->where('category_id', $asset->category_id);
                })
                ->exists();

            if ($hasActiveSameCategory) {
                throw ValidationException::withMessages([
                    'asset_id' => 'This employee already has a borrowed asset from this category.',
                ]);
            }

            $loan = static::getModel()::create($data);

            if ($loan->is_active) {
                $asset->update([
                    'status' => AssetStatus::Assigned,
                ]);
            }

            return $loan;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
