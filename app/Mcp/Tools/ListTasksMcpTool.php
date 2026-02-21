<?php

namespace App\Mcp\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('list_tasks')]
#[Description('Retrieve tasks for the authenticated user with optional status, priority, and overdue filters.')]
#[IsReadOnly]
class ListTasksMcpTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'overdue_only' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $authUserId = $request->user()?->id;
        $userId = $validated['user_id'] ?? $authUserId;

        if (is_null($userId)) {
            return Response::error('Missing user_id and no authenticated user found.');
        }

        if (! is_null($authUserId) && $userId !== $authUserId) {
            return Response::error('You can only list your own tasks.');
        }

        $query = Task::query()->where('user_id', $userId);

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['priority'])) {
            $query->where('priority', $validated['priority']);
        }

        if (! empty($validated['overdue_only'])) {
            $query->overdue();
        }

        $limit = $validated['limit'] ?? 20;
        $tasks = $query->latest()->limit($limit)->get();

        return Response::json([
            'success' => true,
            'total' => $tasks->count(),
            'tasks' => $tasks->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->toDateString(),
                'is_overdue' => $task->isOverdue(),
                'ai_priority_score' => $task->ai_priority_score,
                'ai_reasoning' => $task->ai_reasoning,
                'category_id' => $task->category_id,
                'tags' => $task->tags ?? [],
                'completed_at' => $task->completed_at?->toIso8601String(),
            ])->values()->all(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()->description('Optional. Defaults to authenticated user id.'),
            'status' => $schema->string()->enum(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => $schema->string()->enum(['low', 'medium', 'high', 'urgent']),
            'overdue_only' => $schema->boolean()->default(false),
            'limit' => $schema->integer()->default(20),
        ];
    }


    /*
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id'      => $schema->integer()->required(),
            'status'       => $schema->string()->enum(['pending','in_progress','completed','cancelled']),
            'overdue_only' => $schema->boolean()->default(false),
            'limit'        => $schema->integer()->default(20),
        ];
    }


    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'status'  => 'nullable|in:pending,in_progress,completed,cancelled',
            'limit'   => 'integer|min:1|max:100',
        ], [
            'user_id.required' => 'You must provide a user_id.',
        ]);


        $tasks = Task::where('user_id', $validated['user_id'])
            ->when($validated['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($request['overdue_only'] ?? false, fn($q) => $q->overdue())
            ->latest()->limit($validated['limit'] ?? 20)->get();


        // MCP tools return Response::structured() for parseable data
        return Response::structured([
            'total' => $tasks->count(),
            'tasks' => $tasks->map(fn($t) => [
                'id'       => $t->id, 'title' => $t->title,
                'status'   => $t->status, 'priority' => $t->priority,
                'due_date' => $t->due_date?->toDateString(),
                'is_overdue' => $t->isOverdue(),
            ])->toArray(),
        ]);
    }
    */
}
