<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function authorize(User $user, Employee $employee)
    {
        return $user->id == $employee->user_id;
    }
}
