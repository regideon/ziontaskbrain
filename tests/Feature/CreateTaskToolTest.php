<?php

namespace Tests\Feature;

use App\Ai\Tools\CreateTaskTool;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class CreateTaskToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_null_due_date_when_tool_receives_empty_string(): void
    {
        $user = User::factory()->create();

        $tool = new CreateTaskTool();
        $tool->handle(new Request([
            'user_id' => $user->id,
            'title' => 'Organize notification-related emails for Westway project',
            'description' => 'Organize emails for notification functionality.',
            'priority' => 'medium',
            'due_date' => '',
            'tags' => ['westway', 'email', 'notifications'],
        ]));

        $task = Task::query()->firstOrFail();

        $this->assertNull($task->due_date);
    }

    public function test_it_keeps_a_valid_due_date(): void
    {
        $user = User::factory()->create();

        $tool = new CreateTaskTool();
        $tool->handle(new Request([
            'user_id' => $user->id,
            'title' => 'Send quarterly summary',
            'priority' => 'high',
            'due_date' => '2026-03-01',
        ]));

        $task = Task::query()->firstOrFail();

        $this->assertSame('2026-03-01', $task->due_date?->toDateString());
    }
}
