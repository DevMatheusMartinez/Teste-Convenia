<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts, SkipsOnFailure
{
    use Importable, SkipsFailures, RemembersRowNumber;

    private $allRowsCount = 0;
    private $rowsSuccessCount = 0;
    private $rowsSuccess = [];
    private $rowsFailedCount = 0;
    /**
     * @var Failure[]
     */
    protected $failures = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->allRowsCount++;
        $model = new Employee([
            'name' =>  $row['name'],
            'email' => $row['e_mail'],
            'document' => $row['document'],
            'city' => $row['city'],
            'state' => $row['state'],
            'start_date' => $row['start_date'],
            'user_id' => auth()->user()->id
        ]);

        $this->modelArray = $model->toArray();

        $this->rowsSuccessCount++;
        array_push($this->rowsSuccess, "Linha {$this->getRowNumber()} inserida com successo");

        return $model;
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
            'start_date' => ['required', 'date', 'before_or_equal:'.date('Y-m-d')]
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
            'e_mail.string' => $string,
            'document.required' => $required,
            'document.numeric' => ":attribute deve ser um numero",
            'city.required' => $required,
            'city.string' => $string,
            'state.required' => $required,
            'state.string' => $string,
            'start_date.required' => $required,
            'start_date.string' => $string,
            'start_date.before_or_equal' => ":attribute tem uma data maior que a de hoje"
        ];
    }

    public function customValidationAttributes()
    {
        return ['e_email' => 'e-mail'];
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

    public function getRowsFailedCount()
    {
        return $this->rowsFailedCount;
    }
}
