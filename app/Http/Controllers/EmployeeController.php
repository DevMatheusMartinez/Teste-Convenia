<?php

namespace App\Http\Controllers;

use App\Functions\ValidFiles;
use App\Http\Requests\EmployeeStore;
use App\Imports\EmployeeImport;
use App\Mail\SendMailUser;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    public function get(): JsonResponse
    {
        return response()->json(
            Employee::where('user_id', auth()->user()->id)->get()
        );
    }

    public function store(EmployeeStore $request)
    {
        $validFile = ValidFiles::validFile($request->file);

        if ($validFile != null) {
            return $validFile;
        }

        try {
            $import = new EmployeeImport();
            $import->import($request->file, null, \Maatwebsite\Excel\Excel::CSV);
            $userLogged = auth()->user();
            $post = new SendMailUser($userLogged, $import->numberRegistered());
            Mail::to($userLogged->email)->send($post);
            return response()->json("Upload success", 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return response()->json("NOT ACCEPTABLE: {$e->failures()[0]->errors()[0]}", 406);
        }
      
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
