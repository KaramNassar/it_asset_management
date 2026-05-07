<?php

namespace App\Models;

use App\AssetStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'serial_number',
    'model',
    'category_id',
    'status',
    'purchase_date',
])]
class Asset extends Model
{
    use HasFactory;
    protected $casts = [
        'status' => AssetStatus::class,
        'purchase_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    
}
