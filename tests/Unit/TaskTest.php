<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()
            ->for($user)
            ->create([
                'title' => 'Test Task',
                'description' => 'This is a test task',
                'status' => Task::STATUS_PENDING,
            ]);
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => Task::STATUS_PENDING,
            'user_id' => $user->id,
        ]);
    }
    
    public function test_task_belongs_to_user(): void
    {
        $user = User::factory()->create();
        
        $task = Task::factory()
            ->for($user)
            ->create();
        
        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }
    
    public function test_task_status_enum(): void
    {
        // Test pending status
        $task = Task::factory()->pending()->create();
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertNull($task->completed_at);
        
        // Test in progress status
        $task = Task::factory()->inProgress()->create();
        $this->assertEquals(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertNull($task->completed_at);
        
        // Test completed status
        $task = Task::factory()->completed()->create();
        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
        $this->assertNotNull($task->completed_at);
    }
}
