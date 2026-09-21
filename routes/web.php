<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::middleware('role:admin,hr')->group(function () {
        // Route::inertia('employees', 'employees/index')->name('employees.index');
        // Route::inertia('employees/create', 'employees/create')->name('employees.create');
        // Route::inertia('employees/{employee}', 'employees/show')->name('employees.show');
        // Route::inertia('employees/{employee}/edit', 'employees/edit')->name('employees.edit');

        // Route::inertia('departments', 'departments/index')->name('departments.index');
        // Route::inertia('departments/create', 'departments/create')->name('departments.create');
        // Route::inertia('departments/{department}', 'departments/show')->name('departments.show');
        // Route::inertia('departments/{department}/edit', 'departments/edit')->name('departments.edit');

        // Route::inertia('positions', 'positions/index')->name('positions.index');
        // Route::inertia('positions/create', 'positions/create')->name('positions.create');
        // Route::inertia('positions/{position}', 'positions/show')->name('positions.show');
        // Route::inertia('positions/{position}/edit', 'positions/edit')->name('positions.edit');

        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::patch('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('positions', [PositionController::class, 'index'])->name('positions.index');
        Route::post('positions', [PositionController::class, 'store'])->name('positions.store');
        Route::patch('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');
    });

});

require __DIR__.'/settings.php';
