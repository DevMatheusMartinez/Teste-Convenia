<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'document' => $this->faker->numerify('###########'),
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'start_date' => $this->faker->date('Y-m-d'),
        ];
    }
}
