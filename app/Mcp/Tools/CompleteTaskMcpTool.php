<?php

namespace App\Mcp\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Name('complete_task')]
#[Description('Mark one of the authenticated user\'s tasks as completed.')]
#[IsDestructive]
class CompleteTaskMcpTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'task_id' => 'required|integer|exists:tasks,id',
        ]);

        $authUserId = $request->user()?->id;
        $userId = $validated['user_id'] ?? $authUserId;

        if (is_null($userId)) {
            return Response::error('Missing user_id and no authenticated user found.');
        }

        if (! is_null($authUserId) && $userId !== $authUserId) {
            return Response::error('You can only complete your own tasks.');
        }

        $updated = Task::query()
            ->where('id', $validated['task_id'])
            ->where('user_id', $userId)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        if ($updated === 0) {
            return Response::error('Task not found for this user.');
        }

        return Response::json([
            'success' => true,
            'task_id' => $validated['task_id'],
            'status' => 'completed',
            'completed_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()->description('Optional. Defaults to authenticated user id.'),
            'task_id' => $schema->integer()->required(),
        ];
    }
}
