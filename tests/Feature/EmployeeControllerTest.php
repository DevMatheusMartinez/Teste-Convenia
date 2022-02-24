<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Passport::actingAs(
            User::factory()->create(),
            ['create-servers']
        );
    }

    public function test_it_gets_all_employees_with_a_auth_user()
    {
        $employees = Employee::factory(3)->create([
            'user_id' => $this->user->id
        ]);

        $this->get(route('employees.get'))
            ->assertOk()
            ->assertJson(
                $employees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'user_id' => $employee->user_id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'document' => $employee->document,
                        'city' => $employee->city,
                        'state' => $employee->state,
                        'start_date' => $employee->start_date,
                    ];
                })->toArray()
            );
    }

    public function test_it_cannot_gets_employees_from_another_user()
    {
        $user = User::factory()->create();

        Employee::factory(3)->create([
            'user_id' => $user->id
        ]);

        $this->get(route('employees.get'))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_it_shows_a_employee_with_a_auth_user()
    {
        $employee = Employee::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->get(route('employees.show', [$employee->id]),)
            ->assertOk()
            ->assertJson([
                'id' => $employee->id,
                'user_id' => $employee->user_id,
                'name' => $employee->name,
                'email' => $employee->email,
                'document' => $employee->document,
                'city' => $employee->city,
                'state' => $employee->state,
                'start_date' => $employee->start_date,
            ]);
    }

    public function test_it_cannot_shows_a_employee_from_another_user()
    {
        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id
        ]);

        $this->get(route('employees.show', [$employee->id]))
            ->assertForbidden()
            ->assertSee('This action is unauthorized.');
    }

    public function test_it_deletes_a_employee_with_a_auth_user()
    {
        $employee = Employee::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id
        ]);

        $this->delete(route('employees.destroy', [$employee->id]),)
            ->assertOk()
            ->assertExactJson([
                'Success' => true
            ]);

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id
        ]);
    }

    public function test_it_cannot_deletes_a_employee_from_another_user()
    {
        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'user_id' => $user->id
        ]);

        $this->delete(route('employees.destroy', [$employee->id]),)
            ->assertForbidden()
            ->assertSee('This action is unauthorized.');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'user_id' => $user->id
        ]);
    }
}
