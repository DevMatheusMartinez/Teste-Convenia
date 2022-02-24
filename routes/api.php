<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => 'api'], function() {
   Route::post('login', [AuthController::class, 'authenticate']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'get'])
        ->name('employees.get');

    Route::get('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])
        ->name('employees.show');

    Route::post('/employees', [EmployeeController::class, 'store']);

    Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])
        ->name('employees.destroy');
});
