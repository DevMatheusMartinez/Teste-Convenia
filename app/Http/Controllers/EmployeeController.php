<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStore;
use App\Imports\EmployeeImport;
use App\Mail\SendMailUser;
use App\Models\Employee;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Excel;

class EmployeeController extends Controller
{
    public function get(): JsonResponse
    {
        return response()->json(Employee::get());
    }

    public function store(EmployeeStore $request): JsonResponse
    {
        try {
            $import = new EmployeeImport();
            $import->import($request->file, null, Excel::CSV);
            $importReport = $import->getImportReport();
            $errorsReport = $import->getErrorsReport();
            $userLogged = auth()->user();
        } catch (Exception $e) {
            return response()->json("NOT ACCEPTABLE: {$e->getMessage()}", 406);
        }

        $post = new SendMailUser($userLogged, $importReport, $errorsReport);
        Mail::to($userLogged->email)->send($post);
        
        return response()->json(["report" => array_merge($importReport, $errorsReport)], 200);
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
