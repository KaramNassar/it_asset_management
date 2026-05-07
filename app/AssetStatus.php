<?php

namespace App;

enum AssetStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case Maintenance = 'maintenance';
    case Broken = 'broken';
    case Archived = 'archived';

    public function color(): string
    {
        return match($this) {
            self::Available => 'green',
            self::Assigned => 'blue',
            self::Maintenance => 'yellow',
            self::Broken => 'red',
            self::Archived => 'gray',
        };
    }
}
