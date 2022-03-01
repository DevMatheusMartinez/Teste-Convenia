<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class EmployeeImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $allRowsCount = 0;
    private $rowsSuccessCount = 0;
    private $rowsSuccess = [];
    private $rowsFailedCount = 0;
    /**
     * @var Failure[]
     */
    protected $failures = [];

    public function onRow(Row $row)
    {
        $this->allRowsCount++;
        $rowNumber = $row->getIndex();
        $row = $row->toArray();

        $employee = Employee::updateOrCreate([
            'email' => $row['e_mail'],
            'user_id' => auth()->user()->id
        ], [
            'name' =>  $row['name'],
            'document' => $row['document'],
            'city' => $row['city'],
            'state' => $row['state'],
            'start_date' => $row['start_date'],
        ]);

        $this->rowsSuccessCount++;
        array_push($this->rowsSuccess, "Linha {$rowNumber} inserida com successo");

        return $employee;
    }

    public function getImportReport(): array
    {
        date_default_timezone_set('America/Sao_Paulo');
        return [
            "allRowsCount" => $this->allRowsCount,
            "rowsSuccessCount" => $this->rowsSuccessCount,
            "rowsSuccess" => $this->rowsSuccess,
            "importDate" => date("d/m/Y"),
            "importTime" => date('H:i')
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:25', 'min:4'],
            'e_mail' => ['required', 'email'],
            'document' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'start_date' => ['required', 'date', 'before_or_equal:' . date('Y-m-d')]
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        $required = ':attribute é obrigátorio.';
        $string =  ':attribute deve ser uma string.';

        return [
            'name.required' => $required,
            'name.string' => $string,
            'name.max' => ':attribute deve ter no maximo 25 caracteres.',
            'name.min' => ':attribute deve ter no minimo 4 caracteres.',
            'e_mail.required' => $required,
            'e_mail.email' => ":attribute deve ser um email válido",
            'document.required' => $required,
            'document.numeric' => ":attribute deve ser um numero",
            'city.required' => $required,
            'city.string' => $string,
            'state.required' => $required,
            'state.string' => $string,
            'start_date.required' => $required,
            'start_date.date' => ":attribute deve ser uma data válida",
            'start_date.before_or_equal' => ":attribute tem uma data maior que a de hoje"
        ];
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        $this->allRowsCount++;
        $this->rowsFailedCount++;

        $this->failures = array_merge($this->failures, $failures);
    }

    public function getErrorsReport(): array
    {
        $errorsReport = [
            "rowsFailedCount" => $this->rowsFailedCount,
            "errors" => []
        ];

        foreach ($this->failures as $failure) {
            array_push(
                $errorsReport["errors"],
                "Linha {$failure->row()} {$failure->errors()[0]}"
            );
        }

        return $errorsReport;
    }
}
