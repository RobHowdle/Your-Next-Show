<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TodoTestDataSeeder extends Seeder
{
    public function run()
    {

        $faker = Faker::create();

        // Create 12 todo items with serviceable_id = 1
        $this->createTodoItems($faker, 1, 4); // 4 completed

        // Create 1 deleted todo item with serviceable_id = 1
        $this->createDeletedTodoItem($faker, 1);

        // Create another 12 todo items with serviceable_id = 2
        $this->createTodoItems($faker, 2, 4); // 4 completed
    }

    private function createTodoItems($faker, $serviceableId, $completedCount)
    {
        $user = User::where('first_name', 'Promoter')->firstOrFail();
        $userId = $user->id;
        // Create 12 todo items
        for ($i = 0; $i < 12; $i++) {
            // Randomly determine if the item should be completed
            $completed = $i < $completedCount; // First X items are completed

            // Create a Todo item
            Todo::create([
                'user_id' => $userId,
                'serviceable_id' => $serviceableId,
                'serviceable_type' => 'App\Models\Promoter',
                'item' => $faker->sentence(6), // Random todo item
                'completed' => $completed,
                'completed_at' => $completed ? now() : null, // Set completed_at if completed
            ]);
        }
    }

    private function createDeletedTodoItem($faker, $serviceableId)
    {
        $user = User::where('first_name', 'Promoter')->firstOrFail();
        $userId = $user->id;
        // Create 1 deleted todo item
        Todo::create([
            'user_id' => $userId,
            'serviceable_id' => $serviceableId,
            'serviceable_type' => 'App\Models\Promoter',
            'item' => $faker->sentence(6), // Random todo item
            'completed' => false,
            'completed_at' => null,
            'deleted_at' => now(), // Soft delete the item
        ]);
    }
}
