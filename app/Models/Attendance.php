<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;
    protected $fillable = ['employee_id', 'work_date', 'status', 'check_in', 'check_out'];

    #[Override]
    protected function casts()
    {
        return [
            'work_date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }


    public function employee() : BelongsTo
     {
        return $this->belongsTo(Employee::class);
    }
}
