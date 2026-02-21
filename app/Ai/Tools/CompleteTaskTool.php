<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

use App\Models\Task;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Mark a task as completed by its ID. This action cannot be undone.')]
#[IsDestructive]
class CompleteTaskTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()
                ->description('The ID of the task to mark as completed.')
                ->required(),
        ];
    }


    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'task_id' => 'required|integer|exists:tasks,id',
        ], [
            'task_id.exists' => 'No task found with that ID. Use list_tasks to find valid task IDs.',
        ]);


        $updated = Task::where('id', $validated['task_id'])->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);


        return $updated
            ? Response::text('Task marked as completed successfully.')
            : Response::error('Failed to update task status.');
    }
}
