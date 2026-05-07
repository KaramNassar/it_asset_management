<?php

namespace App;

enum AssetCondition: string
{
    case Excelent = 'excelent';
    case Good = 'good';
    case Fair = 'fair';
    case Damaged = 'damaged';

    public function color(): string
    {
        return match ($this) {
            self::Excelent => 'success',
            self::Good => 'info',
            self::Fair => 'warning',
            self::Damaged => 'danger',
        };
    }
}
