<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class showEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function testItShowsAEmployeeWithAAuthUser():void
    {
        $employee = Employee::factory()->create([
            'user_id' => $this->actingAs()->id
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

    public function testItCannotShowsAEmployeeFromAnotherUser():void
    {
        $this->actingAs();

        $anotherUser = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $anotherUser->id
        ]);

        $this->get(route('employees.show', [$employee->id]))
            ->assertForbidden()
            ->assertSee('This action is unauthorized.');
    }

    public function testShowsThatAnEmployeeWithoutUserAuthenticationShouldGiveError():void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id
        ]);

        $this->withHeaders(['Accept' => 'application/json'])
        ->get(route('employees.show', [$employee->id]))
        ->assertUnauthorized();
    }

   
}
