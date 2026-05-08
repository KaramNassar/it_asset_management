<?php

namespace App\Filament\Resources\Loans\Schemas;

use App\AssetCondition;
use App\AssetStatus;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Loan;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('asset_id', null)),

                Select::make('category_id')
                    ->label('Category')
                    ->options(fn () => Category::query()->pluck('name', 'id')->all())
                    ->default(fn ($record) => $record?->asset?->category_id)
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('asset_id', null))
                    ->dehydrated(false)
                    ->required(),

                Select::make('asset_id')
                    ->label('Asset')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if (! $state) {
                            $set('condition_on_delivery', AssetCondition::Excelent->value);

                            return;
                        }

                        $lastInspectedLoan = Loan::query()
                            ->where('asset_id', $state)
                            ->whereNotNull('condition_on_return')
                            ->latest('returned_at')
                            ->first();

                        $set(
                            'condition_on_delivery',
                            $lastInspectedLoan?->condition_on_return?->value ?? AssetCondition::Excelent->value
                        );
                    })
                    ->disabled(fn (Get $get): bool => blank($get('employee_id')) || blank($get('category_id')))
                    ->options(function (Get $get) {
                        $categoryId = $get('category_id');

                        if (! $categoryId) {
                            return [];
                        }

                        return Asset::query()
                            ->where('category_id', $categoryId)
                            ->where('status', AssetStatus::Available)
                            ->pluck('serial_number', 'id')
                            ->all();
                    })
                    ->rule(function (Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $employeeId = $get('employee_id');
                            $categoryId = $get('category_id');

                            if (! $employeeId || ! $categoryId || ! $value) {
                                return;
                            }

                            $hasActiveSameCategory = Loan::query()
                                ->where('employee_id', $employeeId)
                                ->where('is_active', true)
                                ->whereHas('asset', fn ($q) => $q->where('category_id', $categoryId))
                                ->exists();

                            if ($hasActiveSameCategory) {
                                $fail('This employee already has a borrowed asset from this category that has not been returned yet.');

                                return;
                            }

                            $assetAlreadyLoaned = Loan::query()
                                ->where('asset_id', $value)
                                ->where('is_active', true)
                                ->exists();

                            if ($assetAlreadyLoaned) {
                                $fail('This asset is currently borrowed.');
                            }
                        };
                    }),

                DateTimePicker::make('loaned_at')
                    ->required()
                    ->default(now()),

                Select::make('condition_on_delivery')
                    ->label('Condition on delivery')
                    ->options(AssetCondition::class)
                    ->default(AssetCondition::Excelent->value)
                    ->required()
                    ->disabled(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
