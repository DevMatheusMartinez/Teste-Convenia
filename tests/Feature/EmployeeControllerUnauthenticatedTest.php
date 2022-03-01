<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class EmployeeControllerUnauthenticatedTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function test_he_gets_error_message_when_requesting_all_employees_without_an_authenticated_user()
    {
        $this->withHeaders(['Accept' => 'application/json'])->get(route('employees.get'))->assertUnauthorized();
    }

    public function test_shows_that_an_employee_without_user_authentication_should_give_error()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id
        ]);

        $this->withHeaders(['Accept' => 'application/json'])
        ->get(route('employees.show', [$employee->id]))
        ->assertUnauthorized();
    }

    public function test_deletes_an_employee_without_user_authentication_should_give_an_error()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id
        ]);
        
   
        $this->withHeaders(['Accept' => 'application/json'])->delete(route('employees.destroy', [$employee->id]),)
            ->assertUnauthorized();

        $this->assertDatabaseHas('employees', [
                'id' => $employee->id
        ]);
    }

    public function test_it_registers_employees_without_user_authentication_should_give_error()
    {
        $file = UploadedFile::fake()->create('fakeExcel.csv');

        Excel::fake();

        $this->withHeaders(['Accept' => 'application/json'])->post(route('employees.store'), [
            'file' => $file
        ])->assertUnauthorized();
    }
}
