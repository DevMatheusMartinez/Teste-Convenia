<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Employee([
            'name' =>  $row['name'],
            'email' => $row['e_mail'],
            'document' => $row['document'],
            'city' => $row['city'],
            'state' => $row['state'],
            'start_date' => $row['start_date'],
            'user_id' => auth()->user()->id
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:25', 'min:4'],
            'e_mail' => ['required', 'email'],
            'document' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'start_date' => ['required', 'date']
        ];
    }
}
