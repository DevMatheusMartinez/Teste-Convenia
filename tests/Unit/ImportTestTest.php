<?php

namespace Tests\Unit\Requests;

use App\Imports\EmployeeImport;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

class ImportTestTest extends TestCase
{
    use RefreshDatabase;

    public function createFile($arrayData, $path): EmployeeImport
    {
        $import = new EmployeeImport();
        $spreadsSheet = new Spreadsheet();

        $spreadsSheet->getActiveSheet()
            ->fromArray(
                $arrayData,
                NULL,
                'A1'
            );

        $writer = new Csv($spreadsSheet);
        $writer->save($path);

        $import->import($path);

        return $import;
    }

    public function testImportCompleteDataShouldGiveFourSuccessfullLines(): void
    {
        $this->actingAs();

        $path = "tests/Files/fullSuccessLines.csv";

        $import = $this->createFile([
            ['name', 'e_mail', 'document', 'city', 'state', 'start_date'],
            ['Bob Wilson', 'lm@mats3uda.com.br',  '13001647000', 'Salvador', 'BA', '2019-06-09'],
            ['Laura Matsuda', 'bob@gmail.com',  '60095284028', 'Niterói', 'RJ', '2019-06-08'],
            ['Marco Rodrigues', 'marco@kyokugen.org',  '71306511054', 'Osasco', 'SC', '2021-01-10'],
            ['Christie Monteiro', 'monteiro@naamco.com',  '28586454001', 'Recife', 'PE', '2015-11-03']
        ], "tests/Files/fullSuccessLines.csv");

        $importReport = $import->getImportReport();
        $errorsReport = $import->getErrorsReport();

        assertEquals($importReport['allRowsCount'], 4);
        assertEquals($importReport['rowsSuccessCount'], 4);
        assertEquals(
            $importReport['rowsSuccess'],
            [
                0 => "Linha 2 inserida com successo",
                1 => "Linha 3 inserida com successo",
                2 => "Linha 4 inserida com successo",
                3 => "Linha 5 inserida com successo"
            ]
        );

        $this->assertDatabaseCount('employees', 4);

        assertEquals($errorsReport['rowsFailedCount'], 0);
        assertEmpty($errorsReport['errors']);

        unlink($path);
    }

    public function testImportDataWithErrorsShouldGiveYouSomeLinesWithError(): void
    {
        $this->actingAs();

        $path = "tests/Files/successAndFailure.csv";

        $import = $this->createFile([
            ['name', 'e_mail', 'document', 'city', 'state', 'start_date'],
            ['Matheus da Silva', 'matheus@gmail.com', null, 'Salvador', 'BA', '2019-06-09'],
            ['Ricardo', 'Jo.1@gmail.com',  null, null, 'RJ', '2019-06-08'],
            ['João Kleber', 'meuEmail@gmail.com', '13455669772', 'Osasco', 'SC', '2021-01-10'],
            ['Antonio Luiz', 'Antonio@gmail.com',  '32892132733', 'Recife', '2342', '2015-11-03'],
            [null, 'Edu@gmail', '22333343443', 'Rio Preto', 'SP', '2033-09-12'],
            ["Yago Martins", 'Y@gmail.com', '3287387213', 'Rio Preto', 'SP', '2022-01-12'],
            ["Caio Roberto", 'traqaqq', '34562212344', 'Rio Preto', 'SP', '2022-01-12'],
        ], $path);

        $importReport = $import->getImportReport();
        $errorsReport = $import->getErrorsReport();

        assertEquals($importReport['allRowsCount'], 7);
        assertEquals($importReport['rowsSuccessCount'], 2);
        assertEquals(
            $importReport['rowsSuccess'],
            [
                0 => "Linha 4 inserida com successo",
                1 => "Linha 7 inserida com successo"
            ]
        );

        $this->assertDatabaseCount('employees', 2);
        assertEquals($errorsReport['rowsFailedCount'], 5);
        assertEquals(
            $errorsReport['errors'],
            [
                0 => "Linha 2 document é obrigátorio.",
                1 => "Linha 3 document é obrigátorio.",
                2 => "Linha 3 city é obrigátorio.",
                3 => "Linha 5 state deve ser uma string.",
                4 => "Linha 6 name é obrigátorio.",
                5 => "Linha 6 start_date tem uma data maior que a de hoje",
                6 => "Linha 8 e_mail deve ser um email válido",
            ]
        );

        unlink($path);
    }

    public function testImportLineWithExistingEmailInTheDatabaseIsUpdated(): void
    {
        $user = $this->actingAs();

        Employee::factory()->create([
            'name' => 'Fulano dos Santos',
            'email' => 'fulanosantos@gmail.com',
            'document' => '2332393133',
            'city' => 'Rio Preto',
            'state' => 'SP',
            'start_date' => '2019-6-22',
            'user_id' => $user->id
        ]);

        $path = "tests/Files/successAndFailure.csv";

        $import = $this->createFile([
            ['name', 'e_mail', 'document', 'city', 'state', 'start_date'],
            ['Fulano Editado', 'fulanosantos@gmail.com', '224556312335', 'Salvador', 'BA', '2019-06-09'],
        ], $path);

        $importReport = $import->getImportReport();
        $errorsReport = $import->getErrorsReport();

        assertEquals($importReport['allRowsCount'], 1);
        assertEquals($importReport['rowsSuccessCount'], 1);
        assertEquals(
            $importReport['rowsSuccess'],
            [
                0 => "Linha 2 inserida com successo"
            ]
        );

        $this->assertDatabaseCount('employees', 1);
        assertEquals($errorsReport['rowsFailedCount'], 0);
        assertEmpty($errorsReport['errors']);

        unlink($path);
    }

    public function testIfTheRulesAreTheSameAsExpected(): void
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

    public function testIfTheCustomValidationMessagessAreTheSameAsExpected(): void
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
