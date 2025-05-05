<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_tasks_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Collection::class, $user->tasks);
        $this->assertCount(0, $user->tasks);
    }

    public function test_user_can_have_multiple_tasks(): void
    {
        $user = User::factory()->create();

        Task::factory()
            ->for($user)
            ->create(['title' => 'Task 1']);

        Task::factory()
            ->for($user)
            ->create(['title' => 'Task 2']);

        $this->assertCount(2, $user->fresh()->tasks);
        $this->assertEquals('Task 1', $user->fresh()->tasks[0]->title);
        $this->assertEquals('Task 2', $user->fresh()->tasks[1]->title);
    }

    public function test_deleting_user_deletes_tasks(): void
    {
        $user = User::factory()->create();

        Task::factory()
            ->for($user)
            ->count(3)
            ->create();

        $this->assertDatabaseCount('tasks', 3);

        $user->delete();

        $this->assertDatabaseCount('tasks', 0);
    }
}
