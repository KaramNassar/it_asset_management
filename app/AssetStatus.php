<?php

namespace App;

use Filament\Support\Contracts\HasColor;

enum AssetStatus: string implements HasColor
{
    case Available = 'available';
    case Assigned = 'assigned';
    case Maintenance = 'maintenance';
    case Broken = 'broken';
    case Archived = 'archived';

    public function getColor(): string
    {
        return match($this) {
            self::Available => 'success',
            self::Assigned => 'info',
            self::Maintenance => 'warning',
            self::Broken => 'danger',
            self::Archived => 'gray',
        };
    }
}
