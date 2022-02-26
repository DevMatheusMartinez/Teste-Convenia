<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => 'api'], function() {
   Route::post('login', [AuthController::class, 'authenticate']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/employees', [EmployeeController::class, 'get'])
        ->name('employees.get');

    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])
        ->name('employees.show');

    Route::post('/employees', [EmployeeController::class, 'store']);

    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->name('employees.destroy');
});
