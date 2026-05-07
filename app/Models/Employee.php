<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['number', 'name', 'department_id', 'branch_id'])]
class Employee extends Model
{
    use HasFactory;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function user()
    {
        return $this->hasOne(User::class);
    }
    
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
