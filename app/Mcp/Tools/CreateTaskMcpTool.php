<?php

namespace App\Mcp\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('create_task')]
#[Description('Create a new task for the authenticated user from structured fields.')]
class CreateTaskMcpTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date_format:Y-m-d',
            'category_id' => 'nullable|integer|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $authUserId = $request->user()?->id;
        $userId = $validated['user_id'] ?? $authUserId;

        if (is_null($userId)) {
            return Response::error('Missing user_id and no authenticated user found.');
        }

        if (! is_null($authUserId) && $userId !== $authUserId) {
            return Response::error('You can only create tasks for your own account.');
        }

        $task = Task::create([
            'user_id' => $userId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'due_date' => $validated['due_date'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'tags' => $validated['tags'] ?? [],
        ]);

        return Response::json([
            'success' => true,
            'task_id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
            'priority' => $task->priority,
            'due_date' => $task->due_date?->toDateString(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()->description('Optional. Defaults to authenticated user id.'),
            'title' => $schema->string()->required(),
            'description' => $schema->string(),
            'priority' => $schema->string()->enum(['low', 'medium', 'high', 'urgent'])->default('medium'),
            'due_date' => $schema->string()->description('Optional. Use YYYY-MM-DD.'),
            'category_id' => $schema->integer(),
            'tags' => $schema->array()->items($schema->string()),
        ];
    }
}
