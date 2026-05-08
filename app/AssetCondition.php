<?php

namespace App;

use Filament\Support\Contracts\HasColor;

enum AssetCondition: string implements HasColor
{
    case Excelent = 'excelent';
    case Good = 'good';
    case Fair = 'fair';
    case Damaged = 'damaged';

    public function getColor(): string
    {
        return match ($this) {
            self::Excelent => 'success',
            self::Good => 'info',
            self::Fair => 'warning',
            self::Damaged => 'danger',
        };
    }
}
