<?php

namespace App\Models;

use App\AssetCondition;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'loaned_at',
    'returned_at',
    'condition_on_delivery',
    'condition_on_return',
    'notes',
    'asset_id',
    'employee_id',
    'is_active'
])]
class Loan extends Model
{
    use HasFactory;
    protected $casts = [
        'loaned_at' => 'datetime',
        'returned_at' => 'datetime',
        'condition_on_delivery' => AssetCondition::class,
        'condition_on_return' => AssetCondition::class,
    ];
    
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
