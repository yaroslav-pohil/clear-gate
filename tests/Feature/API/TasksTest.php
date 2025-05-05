<?php

namespace Tests\Feature\API;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_unauthenticated_users_cannot_access_tasks_endpoints(): void
    {
        // Index
        $this->getJson('/api/tasks')
            ->assertStatus(401);

        // Store
        $this->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
            ])
            ->assertStatus(401);

        // Update
        $this->putJson('/api/tasks/1', [
                'title' => 'Updated Task',
                'description' => 'Updated Description',
            ])
            ->assertStatus(401);

        // Destroy
        $this->deleteJson('/api/tasks/1')
            ->assertStatus(401);
    }

    public function test_authenticated_users_can_get_a_list_of_their_tasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create some tasks for the user
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // Create a tasks for another user
        $otherUser = User::factory()->create();
        Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        // Make the request
        $response = $this->getJson('/api/tasks');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'user_id',
                        'completed_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        // Ensure we only get the tasks belonging to the authenticated user
        $response->assertJsonFragment(['user_id' => $user->id])
            ->assertJsonMissing(['user_id' => $otherUser->id]);
    }

    public function test_authenticated_users_can_create_a_new_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $taskData = [
            'title' => 'New Task Title',
            'description' => 'New Task Description',
            'status' => Task::STATUS_PENDING,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment($taskData)
            ->assertJsonFragment(['user_id' => $user->id])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task Title',
            'description' => 'New Task Description',
            'user_id' => $user->id,
        ]);
    }

    public function test_task_validation_errors(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Missing required fields
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);

        // Invalid status
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_authenticated_users_can_update_their_own_tasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tasks = Task::factory()->pending()->create([
            'user_id' => $user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'status' => Task::STATUS_COMPLETED,
        ];

        $response = $this->putJson("/api/tasks/{$tasks->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment($updatedData)
            ->assertJsonFragment(['id' => $tasks->id])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $tasks->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'status' => Task::STATUS_COMPLETED,
            'completed_at' => now(), // Assert that the completed_at field is set to the current date and time
        ]);
    }

    public function test_users_cannot_update_tasks_that_dont_belong_to_them(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $otherUser = User::factory()->create();
        $tasks = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->putJson("/api/tasks/{$tasks->id}", [
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
        ]);

        $response->assertForbidden();

        // Verify the tasks was not updated
        $this->assertDatabaseHas('tasks', [
            'id' => $tasks->id,
            'title' => $tasks->title,
            'description' => $tasks->description,
        ]);
    }

    public function test_authenticated_users_can_delete_their_own_tasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tasks = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$tasks->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $tasks->id,
        ]);
    }

    public function test_users_cannot_delete_tasks_that_dont_belong_to_them(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $otherUser = User::factory()->create();
        $tasks = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$tasks->id}");

        $response->assertForbidden();

        // Verify the tasks was not deleted
        $this->assertDatabaseHas('tasks', [
            'id' => $tasks->id,
        ]);
    }
}
