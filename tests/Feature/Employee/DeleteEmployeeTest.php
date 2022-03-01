<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function testItDeletesAEmployeeWithAAuthUser(): void
    {
        $employee = Employee::factory()->create([
            'user_id' => $this->actingAs()->id
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id
        ]);

        $this->delete(route('employees.destroy', [$employee->id]),)
            ->assertNoContent();

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id
        ]);
    }

    public function testItCannotDeletesAEmployeeFromAnotherUser():void
    {
        $this->actingAs();
        $anotherUser = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $anotherUser->id
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'user_id' => $anotherUser->id
        ]);

        $this->delete(route('employees.destroy', [$employee->id]),)
            ->assertForbidden()
            ->assertSee('This action is unauthorized.');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'user_id' => $anotherUser->id
        ]);
    }

   
}
