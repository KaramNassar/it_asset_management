<?php

namespace App\Filament\Resources\Loans\Tables;

use App\AssetCondition;
use App\AssetStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loaned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('condition_on_delivery')
                    ->badge()
                    ->searchable(),
                TextColumn::make('condition_on_return')
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('asset.serial_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('return_asset')
                    ->label('Return')
                    ->visible(fn ($record) => (bool) $record->is_active)
                    ->schema([
                        DateTimePicker::make('returned_at')
                            ->default(now())
                            ->required(),
                        Textarea::make('notes'),
                    ])
                    ->action(function (array $data, $record) {
                        DB::transaction(function () use ($data, $record) {
                            $loan = $record->newQuery()
                                ->whereKey($record->getKey())
                                ->lockForUpdate()
                                ->firstOrFail();

                            $asset = $loan->asset()
                                ->lockForUpdate()
                                ->firstOrFail();

                            if (! $loan->is_active) {
                                return;
                            }

                            $loan->update([
                                'returned_at' => $data['returned_at'],
                                'notes' => $data['notes'] ?? $loan->notes,
                                'is_active' => false,
                            ]);

                            $asset->update([
                                'status' => AssetStatus::Maintenance,
                            ]);
                        });
                    }),

                Action::make('finish_inspection')
                    ->label('Finish Inspection')
                    ->visible(fn ($record) => filled($record->returned_at) && blank($record->condition_on_return))
                    ->schema([
                        Select::make('condition_on_return')
                            ->options(AssetCondition::class)
                            ->required(),

                        Select::make('asset_status_after_inspection')
                            ->label('Asset   Status After Inspection')
                            ->options([
                                AssetStatus::Available->value => 'available',
                                AssetStatus::Maintenance->value => 'maintenance',
                                AssetStatus::Broken->value => 'broken',
                            ])
                            ->default(AssetStatus::Available->value)
                            ->required(),

                        Textarea::make('notes'),
                    ])
                    ->action(function (array $data, $record) {
                        DB::transaction(function () use ($data, $record) {
                            $loan = $record->newQuery()
                                ->whereKey($record->getKey())
                                ->lockForUpdate()
                                ->firstOrFail();

                            $asset = $loan->asset()
                                ->lockForUpdate()
                                ->firstOrFail();

                            if (blank($loan->returned_at) || filled($loan->condition_on_return)) {
                                return;
                            }

                            $loan->update([
                                'condition_on_return' => $data['condition_on_return'],
                                'notes' => $data['notes'] ?? $loan->notes,
                            ]);

                            $asset->update([
                                'status' => $data['asset_status_after_inspection'],
                            ]);
                        });
                    }),
            ]);
    }
}
