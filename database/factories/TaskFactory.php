<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'is_completed' => $this->faker->boolean(30),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}