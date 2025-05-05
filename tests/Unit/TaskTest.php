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
            ]);
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
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
    
    public function test_task_completion(): void
    {
        // Test uncompleted task
        $task = Task::factory()->create();
        $this->assertNull($task->completed_at);
        
        // Test completed task
        $task = Task::factory()->completed()->create();
        $this->assertNotNull($task->completed_at);
    }
}
