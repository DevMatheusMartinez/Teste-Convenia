<?php

namespace Tests\Unit\Requests;

use App\Imports\EmployeeImport;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class ImportTestTest extends TestCase
{
    public function test_atim()
    {
        $teste = new EmployeeImport();
        $spreadsSheet = new Spreadsheet();
        $arrayData = [
            ['Bob Wilson', 'lm@mats3uda.com.br',  '13001647000', 'Salvador', 'BA', '2019-06-09'],
            ['Laura Matsuda', 'bob@gmail.com',  '60095284028', 'Niterói', 'RJ', '2019-06-08'],
            ['Marco Rodrigues', 'marco@kyokugen.org',  '71306511054', 'Osasco', 'SC', '2021-01-10'],
            ['Christie Monteiro', 'monteiro@naamco.com',  '28586454001', 'Recife', 'PE', '2015-11-03']
        ];
        $spreadsSheet->getActiveSheet()
            ->fromArray(
                $arrayData,  
                NULL,        
                'A1'         
            );

        $currentRow = null;
        foreach ($spreadsSheet->getActiveSheet()->getRowIterator() as $row) {
            $currentRow = new Row($row, ['name', 'e_mail', 'document', 'city', 'state', 'start_date']);
            $teste->onRow($currentRow);
        }

        // $row = new Row($spreadsSheet);
        // $teste = new EmployeeImport();
    }

    public function test_If_The_Rules_Are_The_Same_As_Expected():void
    {
        $import = new EmployeeImport();

        $this->assertEquals(
            $import->rules(),
            [
                'name' => ['required', 'string', 'max:25', 'min:4'],
                'e_mail' => ['required', 'email'],
                'document' => ['required', 'numeric'],
                'city' => ['required', 'string'],
                'state' => ['required', 'string'],
                'start_date' => ['required', 'date', 'before_or_equal:' . date('Y-m-d')]
            ]
        );
    }

    public function test_If_The_custom_validation_messagess_Are_The_Same_As_Expected():void
    {
        $import = new EmployeeImport();

        $required = ':attribute é obrigátorio.';
        $string =  ':attribute deve ser uma string.';
        $this->assertEquals(
            $import->customValidationMessages(),
            [
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
            ]
        );
    }
}
