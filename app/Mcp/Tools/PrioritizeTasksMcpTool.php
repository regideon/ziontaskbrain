<?php

namespace App\Mcp\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('prioritize_tasks')]
#[Description('Save AI priority scores (1-100) and optional reasoning for multiple tasks.')]
class PrioritizeTasksMcpTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'priority_scores' => 'required|array|min:1',
            'priority_scores.*.task_id' => 'required|integer|exists:tasks,id',
            'priority_scores.*.score' => 'required|integer|min:1|max:100',
            'priority_scores.*.reasoning' => 'nullable|string',
        ]);

        $authUserId = $request->user()?->id;
        $userId = $validated['user_id'] ?? $authUserId;

        if (is_null($userId)) {
            return Response::error('Missing user_id and no authenticated user found.');
        }

        if (! is_null($authUserId) && $userId !== $authUserId) {
            return Response::error('You can only prioritize your own tasks.');
        }

        $updated = 0;
        $skipped = 0;

        foreach ($validated['priority_scores'] as $item) {
            $affected = Task::query()
                ->where('id', $item['task_id'])
                ->where('user_id', $userId)
                ->update([
                    'ai_priority_score' => $item['score'],
                    'ai_reasoning' => $item['reasoning'] ?? null,
                ]);

            if ($affected > 0) {
                $updated++;
            } else {
                $skipped++;
            }
        }

        return Response::json([
            'success' => true,
            'tasks_updated' => $updated,
            'tasks_skipped' => $skipped,
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()->description('Optional. Defaults to authenticated user id.'),
            'priority_scores' => $schema->array()->items(
                $schema->object([
                    'task_id' => $schema->integer()->required(),
                    'score' => $schema->integer()->min(1)->max(100)->required(),
                    'reasoning' => $schema->string(),
                ])
            )->required(),
        ];
    }
}
