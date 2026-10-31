<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestContoller;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportController;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

  // ── Reports & exports (HR & admins) ─────────────────────────────────────
    Route::middleware('role:admin,hr')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/employees.csv', [ReportController::class, 'employees'])->name('reports.employees');
        Route::get('reports/payroll.csv', [ReportController::class, 'payroll'])->name('reports.payroll');
    });

    Route::get('dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::middleware('role:admin,hr')->group(function () {


        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::patch('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('positions', [PositionController::class, 'index'])->name('positions.index');
        Route::post('positions', [PositionController::class, 'store'])->name('positions.store');
        Route::patch('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');


    });
       Route::middleware('role:admin,hr,manager')->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    });

       // Only HR & admins can create, edit or remove employees.
        Route::middleware('role:admin,hr')->group(function () {
            Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::patch('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        });

        Route::middleware('role:admin,hr')->group(function(){
            Route::get('leave-types',[LeaveTypeController::class,'index'])->name('leave-types.index');
            Route::post('leave-types', [LeaveTypeController::class, 'store' ])->name('leave-types.store');
            Route::patch('leave-types/{leaveType}',[LeaveTypeController::class, 'update'])->name('leave-types.update');
            Route::delete('leave-types/{leaveType}', [LeaveTypeController::class, 'destroy' ])->name('leave-types.destroy');
        });

    Route::get('leave-requests',[LeaveRequestContoller::class,'index'])->name('leave-requests.index');
    Route::post('leave-requests',[LeaveRequestContoller::class,'store'])->name('leave-requests.store');




    Route::middleware('role:admin,hr,manager')->group(function () {
        Route::patch('leave-requests/{leaveRequest}/approve', [LeaveRequestContoller::class, 'approve'])->name('leave-requests.approve');
        Route::patch('leave-requests/{leaveRequest}/reject', [LeaveRequestContoller::class, 'reject'])->name('leave-requests.reject');
    });

    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('attendances/clock-in', [AttendanceController::class, 'clockIn'])->name('attendances.clock-in');
    Route::post('attendances/clock-out', [AttendanceController::class, 'clockOut'])->name('attendances.clock-out');


    //Hr admins and managers see the company-wide timesheet
    Route::middleware('role:admin,hr,manager')->group(function(){
        Route::get('timesheets',[AttendanceController::class,'timesheets'])->name('timesheets.index');
    });


    // Paroll (HR & Admins)
    Route::middleware('role:admin,hr')->group(function(){
        Route::get('payslips',[PayslipController::class,'index'])->name('payslips.index');
        Route::post('payslips',[PayslipController::class,'store'])->name('payslips.store');
        Route::get('payslips/{payslip}',[PayslipController::class,'show'])->name('payslips.show');

    });

});

require __DIR__.'/settings.php';
