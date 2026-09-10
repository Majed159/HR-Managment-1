<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    protected $fillable = ['name', 'description', 'code'];
    use HasFactory;

    public function positions() 
    {
        return $this->hasMany(Position::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
