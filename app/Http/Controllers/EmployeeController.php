<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStore;
use App\Jobs\EmployeerImportJob;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function get(): JsonResponse
    {
        return response()->json(Employee::get());
    }

    public function store(EmployeeStore $request): JsonResponse
    {
       $path = Storage::putFile("import", $request->file);
       EmployeerImportJob::dispatch($path)->onQueue('employees');

       return response()->json("teste");
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee);
    }

    public function destroy(Employee $employee): Response
    {
        $employee->delete();

        return response()->noContent();
    }
}
