<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class StoreEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function testItRegistersEmployeesWithAnAuthenticationUser():void
    {
        $this->actingAs();

        $file = UploadedFile::fake()->create('fakeExcel.csv');

        Excel::fake();

        $this->post(route('employees.store'), [
            'file' => $file
        ])->assertOk();

        Excel::assertImported('fakeExcel.csv');
    }

    public function testRegistersEmployeesWithoutFileShouldThrowErrorWithAnAuthenticationUser():void
    {
        $this->actingAs();

        $this->withHeaders(['Accept' => 'application/json'])
            ->post(route('employees.store'))
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "file" => [
                        "Nenhum arquivo foi selecionado"
                    ]
                ]
            ]);
    }

    public function testItRegistersEmployeesWithoutUserAuthenticationShouldGiveError():void
    {
        $file = UploadedFile::fake()->create('fakeExcel.csv');

        Excel::fake();

        $this->withHeaders(['Accept' => 'application/json'])->post(route('employees.store'), [
            'file' => $file
        ])->assertUnauthorized();
    }
}
