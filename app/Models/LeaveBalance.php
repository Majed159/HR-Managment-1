<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveBalanceFactory> */
    use HasFactory;

    protected $fillable = ['employee_id','leave_type_id','year','entitled_days','used_days'];

    protected function remainingDays(): Attribute
    {
        return Attribute::get(fn():int=>$this->entitled_days-$this->used_days);
    }

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);

    }
    public function leaveType() : BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
