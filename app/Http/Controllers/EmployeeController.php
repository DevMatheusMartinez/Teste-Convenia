<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function get(): JsonResponse
    {
        return response()->json(
            Employee::where('user_id', auth()->user()->id)->get()
        );
    }

    public function store()
    {
        Excel::import(new EmployeeImport, request()->file('file'));
        
        return "teste";
    }

    public function show(Employee $employee): JsonResponse
    {
        $this->authorize('employee', $employee);

        return response()->json(
            $employee
        );
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->authorize('employee', $employee);

        return response()->json([
            'Success' => $employee->delete()
        ]);
    }
}
