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
                    ->visible(fn($record) => (bool) $record->is_active)
                    ->form([
                        DateTimePicker::make('returned_at')
                            ->default(now())
                            ->required(),
                        Textarea::make('notes'),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'returned_at' => $data['returned_at'],
                            'notes' => $data['notes'] ?? $record->notes,
                            'is_active' => false,
                        ]);

                        $record->asset()->update([
                            'status' => AssetStatus::Maintenance,
                        ]);
                    }),

                Action::make('finish_inspection')
                    ->label('Finish Inspection')
                    ->visible(fn($record) => filled($record->returned_at) && blank($record->condition_on_return))
                    ->form([
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
                        $record->update([
                            'condition_on_return' => $data['condition_on_return'],
                            'notes' => $data['notes'] ?? $record->notes,
                        ]);

                        $record->asset()->update([
                            'status' => $data['asset_status_after_inspection'],
                        ]);
                    }),
            ]);
    }
}
