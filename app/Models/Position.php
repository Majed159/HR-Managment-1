<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */

    use HasFactory;
    protected $fillable = ['department_id', 'title', 'description'];



    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

   
}
