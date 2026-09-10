<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'department_id',
        'position_id',
        'manager_id',
        'hire_date',
        'employment_status',
        'salary',
        'avatar_path',
        'address',
    ];
    #[Override]
    protected function casts()
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }
    protected function fullName(): Attribute
    {
        return Attribute::get(fn ():string=>"{$this->first_name} {$this->last_name}");

    }
    public function department():BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function  user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function position():BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function manager():BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }


}
