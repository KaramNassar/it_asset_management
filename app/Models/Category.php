<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name'])]
class Category extends Model
{
    use HasFactory;
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
