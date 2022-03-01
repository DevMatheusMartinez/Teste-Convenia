<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsAllEmployeesWithAAuthUser():void
    {
        $user = $this->actingAs();
        $employees = Employee::factory(3)->create([
            'user_id' => $user->id
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

    public function testItCannotGetsEmployeesFromAnotherUser():void
    {
        $this->actingAs();

        $AnotherUser = User::factory()->create();

        Employee::factory(3)->create([
            'user_id' => $AnotherUser->id
        ]);

        $this->get(route('employees.get'))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testHeGetsErrorMessageWhenRequestingAllEmployeesWithoutAnAuthenticatedUser()
    {
        $this->withHeaders(['Accept' => 'application/json'])->get(route('employees.get'))->assertUnauthorized();
    }
}
